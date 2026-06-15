<?php

namespace App\Services;

use App\Models\VillagerRecord;
use App\Services\Contracts\AuditLoggerInterface;
use App\Services\Contracts\LifecycleServiceInterface;
use Illuminate\Support\Facades\DB;

class LifecycleService implements LifecycleServiceInterface
{
    private const ALLOWED_TRANSITIONS = [
        'active' => ['deceased', 'archived'],
        'archived' => ['deceased'],
        'deceased' => [],
    ];

    public function __construct(
        private AuditLoggerInterface $auditLogger,
    ) {}

    public function changeStatus(string $uniqueId, string $newStatus, array $metadata, int $adminId): VillagerRecord
    {
        return DB::transaction(function () use ($uniqueId, $newStatus, $metadata, $adminId) {
            $villager = VillagerRecord::where('unique_id', $uniqueId)->first();

            if (!$villager) {
                throw new \App\Exceptions\RecordNotFoundException(
                    "Villager record not found for ID: {$uniqueId}"
                );
            }

            $currentStatus = $villager->status;
            $allowed = $this->getAllowedTransitions($currentStatus);

            if (!in_array($newStatus, $allowed)) {
                throw new \App\Exceptions\InvalidTransitionException(
                    "Cannot transition from {$currentStatus} to {$newStatus}. Non-active records cannot be reactivated."
                );
            }

            // Validate required metadata
            if ($newStatus === 'deceased' && empty($metadata['date_of_death'])) {
                throw new \Illuminate\Validation\ValidationException(
                    validator([], ['date_of_death' => 'required']),
                );
            }

            if ($newStatus === 'archived') {
                if (empty($metadata['archive_reason'])) {
                    throw new \Illuminate\Validation\ValidationException(
                        validator([], ['archive_reason' => 'required']),
                    );
                }
                if (strlen($metadata['archive_reason']) < 1 || strlen($metadata['archive_reason']) > 500) {
                    throw new \Illuminate\Validation\ValidationException(
                        validator([], ['archive_reason' => 'required|between:1,500']),
                    );
                }
            }

            // Apply status change
            $updateData = ['status' => $newStatus];

            if ($newStatus === 'deceased') {
                $updateData['date_of_death'] = $metadata['date_of_death'];
            }

            if ($newStatus === 'archived') {
                $updateData['archive_reason'] = $metadata['archive_reason'];
            }

            $villager->update($updateData);

            // Audit log
            $this->auditLogger->log('status_change', $adminId, [
                'villager_id' => $uniqueId,
                'description' => "Status changed from {$currentStatus} to {$newStatus} for: {$villager->full_name}",
                'metadata' => [
                    'previous_status' => $currentStatus,
                    'new_status' => $newStatus,
                    'metadata' => $metadata,
                ],
            ]);

            return $villager->fresh();
        });
    }

    public function getAllowedTransitions(string $currentStatus): array
    {
        return self::ALLOWED_TRANSITIONS[$currentStatus] ?? [];
    }
}
