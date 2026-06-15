<?php

namespace App\Services\Contracts;

use App\Models\VillagerRecord;

interface LifecycleServiceInterface
{
    public function changeStatus(string $uniqueId, string $newStatus, array $metadata, int $adminId): VillagerRecord;

    public function getAllowedTransitions(string $currentStatus): array;
}
