<?php

namespace App\Http\Controllers\Api;

use App\Http\Controllers\Controller;
use App\Models\FlaggedRegistration;
use App\Models\VillagerRecord;
use App\Services\Contracts\AuditLoggerInterface;
use App\Services\Contracts\RegistryServiceInterface;
use Illuminate\Http\JsonResponse;
use Illuminate\Http\Request;

class FraudController extends Controller
{
    public function __construct(
        private AuditLoggerInterface $auditLogger,
        private RegistryServiceInterface $registryService,
    ) {}

    public function listFlagged(Request $request): JsonResponse
    {
        $flagged = FlaggedRegistration::where('resolution', 'pending')
            ->with('matchedVillager:id,unique_id,full_name,status')
            ->orderBy('created_at', 'desc')
            ->paginate(25);

        return response()->json($flagged);
    }

    public function resolve(Request $request, int $id): JsonResponse
    {
        $request->validate([
            'resolution' => 'required|in:confirmed_duplicate,confirmed_not_duplicate',
            'justification' => 'required_if:resolution,confirmed_not_duplicate|nullable|string|min:10',
        ]);

        $flagged = FlaggedRegistration::findOrFail($id);

        if (!$flagged->isPending()) {
            return response()->json(['message' => 'This registration has already been resolved.'], 422);
        }

        $flagged->update([
            'resolution' => $request->resolution,
            'justification' => $request->justification,
            'resolved_by' => $request->user()->id,
            'resolved_at' => now(),
        ]);

        // If confirmed not duplicate, create the villager record
        if ($request->resolution === 'confirmed_not_duplicate') {
            $data = $flagged->submitted_data;
            try {
                // Bypass duplicate check since admin resolved it
                $uniqueId = $this->generateUniqueId();
                $villager = VillagerRecord::create([
                    'unique_id' => $uniqueId,
                    'full_name' => $data['full_name'],
                    'date_of_birth' => $data['date_of_birth'],
                    'gender' => $data['gender'],
                    'household_id' => $data['household_id'],
                    'phone_number' => $data['phone_number'] ?? null,
                    'bank_account_number' => $data['bank_account_number'] ?? null,
                    'nin' => $data['nin'] ?? null,
                    'nin_verification_status' => !empty($data['nin']) ? 'pending' : 'not_submitted',
                    'status' => 'active',
                ]);
            } catch (\Exception $e) {
                return response()->json(['message' => 'Failed to create record: ' . $e->getMessage()], 500);
            }
        }

        $this->auditLogger->log('duplicate_resolved', $request->user()->id, [
            'villager_id' => $flagged->matchedVillager?->unique_id,
            'description' => "Flagged registration resolved as: {$request->resolution}",
            'metadata' => [
                'flagged_id' => $id,
                'resolution' => $request->resolution,
                'justification' => $request->justification,
            ],
        ]);

        return response()->json(['message' => 'Flagged registration resolved.']);
    }

    private function generateUniqueId(): string
    {
        $date = now()->format('Ymd');
        $lastRecord = VillagerRecord::where('unique_id', 'like', "ALC-{$date}-%")
            ->orderBy('unique_id', 'desc')
            ->first();

        $nextSequence = $lastRecord ? ((int) substr($lastRecord->unique_id, -5)) + 1 : 1;

        return sprintf('ALC-%s-%05d', $date, $nextSequence);
    }
}
