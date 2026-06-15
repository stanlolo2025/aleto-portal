<?php

namespace App\Services;

use App\Models\AuditLog;
use App\Services\Contracts\AuditLoggerInterface;
use Illuminate\Contracts\Pagination\LengthAwarePaginator;
use Illuminate\Support\Facades\Log;
use Symfony\Component\HttpFoundation\StreamedResponse;

class AuditLoggerService implements AuditLoggerInterface
{
    public function log(string $eventType, ?int $userId, array $details): void
    {
        try {
            AuditLog::create([
                'event_type' => $eventType,
                'event_timestamp' => now(),
                'user_id' => $userId,
                'ip_address' => request()->ip(),
                'affected_villager_id' => $details['villager_id'] ?? null,
                'description' => $details['description'] ?? '',
                'metadata' => $details['metadata'] ?? null,
                'created_at' => now(),
            ]);
        } catch (\Exception $e) {
            // Queue retry if audit log write fails
            Log::error('Audit log write failed', [
                'event_type' => $eventType,
                'error' => $e->getMessage(),
            ]);
            dispatch(new \App\Jobs\AuditRetryJob($eventType, $userId, $details))
                ->delay(now()->addMinute());
        }
    }

    public function query(array $filters, int $page = 1, int $perPage = 100): LengthAwarePaginator
    {
        $query = AuditLog::query()->orderBy('event_timestamp', 'desc');

        if (!empty($filters['event_type'])) {
            $query->where('event_type', $filters['event_type']);
        }

        if (!empty($filters['user_id'])) {
            $query->where('user_id', $filters['user_id']);
        }

        if (!empty($filters['villager_id'])) {
            $query->where('affected_villager_id', $filters['villager_id']);
        }

        if (!empty($filters['date_from'])) {
            $query->where('event_timestamp', '>=', $filters['date_from']);
        }

        if (!empty($filters['date_to'])) {
            $query->where('event_timestamp', '<=', $filters['date_to']);
        }

        return $query->paginate($perPage, ['*'], 'page', $page);
    }

    public function export(array $filters): StreamedResponse
    {
        $query = AuditLog::query()->orderBy('event_timestamp', 'desc');

        if (!empty($filters['event_type'])) {
            $query->where('event_type', $filters['event_type']);
        }
        if (!empty($filters['user_id'])) {
            $query->where('user_id', $filters['user_id']);
        }
        if (!empty($filters['villager_id'])) {
            $query->where('affected_villager_id', $filters['villager_id']);
        }
        if (!empty($filters['date_from'])) {
            $query->where('event_timestamp', '>=', $filters['date_from']);
        }
        if (!empty($filters['date_to'])) {
            $query->where('event_timestamp', '<=', $filters['date_to']);
        }

        return response()->streamDownload(function () use ($query) {
            $handle = fopen('php://output', 'w');
            fputcsv($handle, ['ID', 'Event Type', 'Timestamp', 'User ID', 'IP Address', 'Villager ID', 'Description']);

            $query->chunk(1000, function ($logs) use ($handle) {
                foreach ($logs as $log) {
                    fputcsv($handle, [
                        $log->id,
                        $log->event_type,
                        $log->event_timestamp->toISOString(),
                        $log->user_id,
                        $log->ip_address,
                        $log->affected_villager_id,
                        $log->description,
                    ]);
                }
            });

            fclose($handle);
        }, 'audit_log_export.csv', ['Content-Type' => 'text/csv']);
    }

    public function generateReport(array $filters): array
    {
        $query = AuditLog::query();

        if (!empty($filters['date_from'])) {
            $query->where('event_timestamp', '>=', $filters['date_from']);
        }
        if (!empty($filters['date_to'])) {
            $query->where('event_timestamp', '<=', $filters['date_to']);
        }

        $summary = $query->selectRaw('event_type, COUNT(*) as count, DATE(event_timestamp) as event_date')
            ->groupBy('event_type', 'event_date')
            ->orderBy('event_date', 'desc')
            ->get();

        $totals = $query->selectRaw('event_type, COUNT(*) as count')
            ->groupBy('event_type')
            ->get();

        return [
            'summary' => $summary,
            'totals' => $totals,
            'total_events' => $totals->sum('count'),
        ];
    }
}
