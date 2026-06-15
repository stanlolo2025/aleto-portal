<?php

namespace App\Services\Contracts;

use Illuminate\Contracts\Pagination\LengthAwarePaginator;
use Symfony\Component\HttpFoundation\StreamedResponse;

interface AuditLoggerInterface
{
    public function log(string $eventType, ?int $userId, array $details): void;

    public function query(array $filters, int $page = 1, int $perPage = 100): LengthAwarePaginator;

    public function export(array $filters): StreamedResponse;

    public function generateReport(array $filters): array;
}
