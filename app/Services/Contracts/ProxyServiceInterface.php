<?php

namespace App\Services\Contracts;

use App\Models\ProxyAccount;

interface ProxyServiceInterface
{
    public function assignProxy(string $uniqueId, array $proxyData, int $adminId): ProxyAccount;

    public function removeProxy(string $uniqueId, int $adminId): void;

    public function getProxyCount(string $representativeName): int;
}
