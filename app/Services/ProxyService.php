<?php

namespace App\Services;

use App\Models\ProxyAccount;
use App\Models\VillagerRecord;
use App\Services\Contracts\AuditLoggerInterface;
use App\Services\Contracts\ProxyServiceInterface;
use Illuminate\Support\Facades\DB;

class ProxyService implements ProxyServiceInterface
{
    private const MAX_VILLAGERS_PER_PROXY = 5;
    private const ALLOWED_RELATIONSHIPS = ['spouse', 'child', 'sibling', 'parent', 'grandchild', 'legal_guardian'];

    public function __construct(
        private AuditLoggerInterface $auditLogger,
    ) {}

    public function assignProxy(string $uniqueId, array $proxyData, int $adminId): ProxyAccount
    {
        return DB::transaction(function () use ($uniqueId, $proxyData, $adminId) {
            $villager = VillagerRecord::where('unique_id', $uniqueId)->first();

            if (!$villager) {
                throw new \App\Exceptions\RecordNotFoundException("Villager not found: {$uniqueId}");
            }

            if (!$villager->isActive()) {
                throw new \App\Exceptions\BusinessRuleException(
                    'Proxy accounts can only be assigned to Active villagers.'
                );
            }

            if (!in_array($proxyData['relationship'], self::ALLOWED_RELATIONSHIPS)) {
                throw new \App\Exceptions\BusinessRuleException(
                    'Invalid relationship. Must be one of: ' . implode(', ', self::ALLOWED_RELATIONSHIPS)
                );
            }

            // Check proxy representative limit
            $currentCount = $this->getProxyCount($proxyData['representative_name']);
            if ($currentCount >= self::MAX_VILLAGERS_PER_PROXY) {
                throw new \App\Exceptions\BusinessRuleException(
                    "Proxy representative '{$proxyData['representative_name']}' already serves the maximum of {self::MAX_VILLAGERS_PER_PROXY} villagers."
                );
            }

            // Remove existing proxy if any
            ProxyAccount::where('villager_record_id', $villager->id)->delete();

            // Create new proxy
            $proxy = ProxyAccount::create([
                'villager_record_id' => $villager->id,
                'representative_name' => $proxyData['representative_name'],
                'relationship' => $proxyData['relationship'],
                'proxy_bank_name' => $proxyData['proxy_bank_name'],
                'proxy_bank_account' => $proxyData['proxy_bank_account'],
            ]);

            $this->auditLogger->log('proxy_assigned', $adminId, [
                'villager_id' => $uniqueId,
                'description' => "Proxy assigned: {$proxyData['representative_name']} for {$villager->full_name}",
                'metadata' => [
                    'representative_name' => $proxyData['representative_name'],
                    'relationship' => $proxyData['relationship'],
                ],
            ]);

            return $proxy;
        });
    }

    public function removeProxy(string $uniqueId, int $adminId): void
    {
        DB::transaction(function () use ($uniqueId, $adminId) {
            $villager = VillagerRecord::where('unique_id', $uniqueId)->with('proxyAccount')->first();

            if (!$villager) {
                throw new \App\Exceptions\RecordNotFoundException("Villager not found: {$uniqueId}");
            }

            if (!$villager->proxyAccount) {
                throw new \App\Exceptions\BusinessRuleException('No proxy account to remove.');
            }

            $proxyName = $villager->proxyAccount->representative_name;
            $villager->proxyAccount->delete();

            $this->auditLogger->log('proxy_removed', $adminId, [
                'villager_id' => $uniqueId,
                'description' => "Proxy removed: {$proxyName} from {$villager->full_name}",
                'metadata' => ['representative_name' => $proxyName],
            ]);
        });
    }

    public function getProxyCount(string $representativeName): int
    {
        return ProxyAccount::where('representative_name', $representativeName)->count();
    }
}
