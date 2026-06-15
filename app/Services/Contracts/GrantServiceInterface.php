<?php

namespace App\Services\Contracts;

use App\Models\BeneficiaryList;
use Illuminate\Database\Eloquent\Collection;
use Symfony\Component\HttpFoundation\StreamedResponse;

interface GrantServiceInterface
{
    public function getEligibleVillagers(string $grantId): Collection;

    public function createBeneficiaryList(string $grantId, array $villagerIds, array $amounts, int $adminId): BeneficiaryList;

    public function approveBeneficiaryList(int $listId, int $adminId): BeneficiaryList;

    public function exportList(int $listId, string $format): StreamedResponse;
}
