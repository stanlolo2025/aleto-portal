<?php

namespace App\Services\Contracts;

use App\Models\VillagerRecord;

interface RegistryServiceInterface
{
    public function register(array $data, int $adminId): VillagerRecord;

    public function update(string $uniqueId, array $data, int $adminId): VillagerRecord;

    public function findByUniqueId(string $uniqueId): ?VillagerRecord;
}
