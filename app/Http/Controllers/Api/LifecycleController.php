<?php

namespace App\Http\Controllers\Api;

use App\Http\Controllers\Controller;
use App\Services\Contracts\LifecycleServiceInterface;
use Illuminate\Http\JsonResponse;
use Illuminate\Http\Request;

class LifecycleController extends Controller
{
    public function __construct(
        private LifecycleServiceInterface $lifecycleService,
    ) {}

    public function changeStatus(Request $request, string $uniqueId): JsonResponse
    {
        $request->validate([
            'status' => 'required|in:active,deceased,archived',
            'date_of_death' => 'nullable|date|before_or_equal:today',
            'archive_reason' => 'nullable|string|min:1|max:500',
        ]);

        try {
            $villager = $this->lifecycleService->changeStatus(
                $uniqueId,
                $request->status,
                [
                    'date_of_death' => $request->date_of_death,
                    'archive_reason' => $request->archive_reason,
                ],
                $request->user()->id
            );

            return response()->json([
                'message' => 'Status updated successfully',
                'data' => $villager,
            ]);
        } catch (\App\Exceptions\RecordNotFoundException $e) {
            return response()->json(['message' => $e->getMessage()], 404);
        } catch (\App\Exceptions\InvalidTransitionException $e) {
            return response()->json(['message' => $e->getMessage()], 422);
        } catch (\Illuminate\Validation\ValidationException $e) {
            return response()->json([
                'message' => 'Validation failed',
                'errors' => $e->errors(),
            ], 422);
        }
    }

    public function getAllowedTransitions(string $uniqueId): JsonResponse
    {
        $villager = \App\Models\VillagerRecord::where('unique_id', $uniqueId)->first();

        if (!$villager) {
            return response()->json(['message' => 'Record not found'], 404);
        }

        return response()->json([
            'current_status' => $villager->status,
            'allowed_transitions' => $this->lifecycleService->getAllowedTransitions($villager->status),
        ]);
    }
}
