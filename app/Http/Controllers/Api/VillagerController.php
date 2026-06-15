<?php

namespace App\Http\Controllers\Api;

use App\Http\Controllers\Controller;
use App\Http\Requests\VillagerRegistrationRequest;
use App\Http\Requests\VillagerUpdateRequest;
use App\Models\VillagerRecord;
use App\Services\Contracts\FraudDetectorInterface;
use App\Services\Contracts\RegistryServiceInterface;
use Illuminate\Http\JsonResponse;
use Illuminate\Http\Request;

class VillagerController extends Controller
{
    public function __construct(
        private RegistryServiceInterface $registryService,
        private FraudDetectorInterface $fraudDetector,
    ) {}

    public function index(Request $request): JsonResponse
    {
        $query = VillagerRecord::query();

        if ($request->has('status')) {
            $query->where('status', $request->status);
        }

        if ($request->has('search')) {
            $query->where('full_name', 'like', '%' . $request->search . '%');
        }

        if ($request->has('household_id')) {
            $query->where('household_id', $request->household_id);
        }

        if ($request->has('gender')) {
            $query->where('gender', $request->gender);
        }

        if ($request->has('village')) {
            $query->where('village', $request->village);
        }

        if ($request->has('ward')) {
            $query->where('ward', $request->ward);
        }

        if ($request->has('age_min')) {
            $query->where('date_of_birth', '<=', now()->subYears((int)$request->age_min)->format('Y-m-d'));
        }

        if ($request->has('age_max')) {
            $query->where('date_of_birth', '>=', now()->subYears((int)$request->age_max)->format('Y-m-d'));
        }

        if ($request->has('education_level')) {
            $query->where('education_level', $request->education_level);
        }

        if ($request->has('marital_status')) {
            $query->where('marital_status', $request->marital_status);
        }

        $villagers = $query->with('registeredByUser:id,name')->orderBy('created_at', 'desc')->paginate($request->get('per_page', 25));

        return response()->json($villagers);
    }

    public function store(VillagerRegistrationRequest $request): JsonResponse
    {
        try {
            $data = $request->validated();

            // Handle passport photo upload
            if ($request->hasFile('passport_photo')) {
                $path = $request->file('passport_photo')->store('passport_photos', 'public');
                $data['passport_photo'] = $path;
            }

            $villager = $this->registryService->register(
                $data,
                $request->user()->id
            );

            return response()->json([
                'message' => 'Villager registered successfully',
                'data' => $villager,
            ], 201);
        } catch (\App\Exceptions\DuplicateDetectedException $e) {
            return response()->json([
                'message' => $e->getMessage(),
                'duplicate_data' => $e->duplicateData,
            ], 409);
        }
    }

    public function show(string $uniqueId): JsonResponse
    {
        $villager = $this->registryService->findByUniqueId($uniqueId);

        if (!$villager) {
            return response()->json([
                'message' => "Record not found for ID: {$uniqueId}",
            ], 404);
        }

        $villager->load(['proxyAccount', 'biometricData', 'history', 'registeredByUser']);

        return response()->json(['data' => $villager]);
    }

    public function update(VillagerUpdateRequest $request, string $uniqueId): JsonResponse
    {
        try {
            $villager = $this->registryService->update(
                $uniqueId,
                $request->validated(),
                $request->user()->id
            );

            return response()->json([
                'message' => 'Villager updated successfully',
                'data' => $villager,
            ]);
        } catch (\App\Exceptions\RecordNotFoundException $e) {
            return response()->json(['message' => $e->getMessage()], 404);
        } catch (\App\Exceptions\DuplicateDetectedException $e) {
            return response()->json([
                'message' => $e->getMessage(),
                'duplicate_data' => $e->duplicateData,
            ], 409);
        }
    }

    public function verifyBiometric(Request $request, string $uniqueId): JsonResponse
    {
        $request->validate(['fingerprint_data' => 'required|string']);

        $result = $this->fraudDetector->verifyIdentity($uniqueId, $request->fingerprint_data);

        return response()->json(['data' => $result]);
    }
}
