<?php

namespace App\Http\Controllers\Api;

use App\Http\Controllers\Controller;
use App\Models\VillagerRecord;
use App\Services\Contracts\ProxyServiceInterface;
use Illuminate\Http\JsonResponse;
use Illuminate\Http\Request;

class ProxyController extends Controller
{
    public function __construct(
        private ProxyServiceInterface $proxyService,
    ) {}

    public function show(string $uniqueId): JsonResponse
    {
        $villager = VillagerRecord::where('unique_id', $uniqueId)->with('proxyAccount')->first();

        if (!$villager) {
            return response()->json(['message' => 'Record not found'], 404);
        }

        return response()->json(['data' => $villager->proxyAccount]);
    }

    public function store(Request $request, string $uniqueId): JsonResponse
    {
        $request->validate([
            'representative_name' => 'required|string|max:255',
            'relationship' => 'required|in:spouse,child,sibling,parent,grandchild,legal_guardian',
            'proxy_bank_name' => 'required|string|max:100',
            'proxy_bank_account' => 'required|string|max:50',
        ]);

        try {
            $proxy = $this->proxyService->assignProxy(
                $uniqueId,
                $request->only(['representative_name', 'relationship', 'proxy_bank_name', 'proxy_bank_account']),
                $request->user()->id
            );

            return response()->json(['message' => 'Proxy assigned', 'data' => $proxy], 201);
        } catch (\App\Exceptions\RecordNotFoundException $e) {
            return response()->json(['message' => $e->getMessage()], 404);
        } catch (\App\Exceptions\BusinessRuleException $e) {
            return response()->json(['message' => $e->getMessage()], 422);
        }
    }

    public function destroy(Request $request, string $uniqueId): JsonResponse
    {
        try {
            $this->proxyService->removeProxy($uniqueId, $request->user()->id);
            return response()->json(['message' => 'Proxy removed']);
        } catch (\App\Exceptions\RecordNotFoundException $e) {
            return response()->json(['message' => $e->getMessage()], 404);
        } catch (\App\Exceptions\BusinessRuleException $e) {
            return response()->json(['message' => $e->getMessage()], 422);
        }
    }
}
