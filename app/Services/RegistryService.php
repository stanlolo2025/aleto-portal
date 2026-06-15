<?php

namespace App\Services;

use App\Models\FlaggedRegistration;
use App\Models\VillagerRecord;
use App\Services\Contracts\AuditLoggerInterface;
use App\Services\Contracts\FraudDetectorInterface;
use App\Services\Contracts\RegistryServiceInterface;
use Illuminate\Support\Facades\DB;

class RegistryService implements RegistryServiceInterface
{
    public function __construct(
        private FraudDetectorInterface $fraudDetector,
        private AuditLoggerInterface $auditLogger,
    ) {}

    public function register(array $data, int $adminId): VillagerRecord
    {
        return DB::transaction(function () use ($data, $adminId) {
            // Check for duplicates
            $duplicateCheck = $this->fraudDetector->checkDuplicates([
                'phone_number' => $data['phone_number'] ?? null,
                'bank_account_number' => $data['bank_account_number'] ?? null,
                'nin' => $data['nin'] ?? null,
            ]);

            if ($duplicateCheck['has_duplicate']) {
                // Create flagged registration
                FlaggedRegistration::create([
                    'submitted_data' => $data,
                    'matched_field' => implode(',', $duplicateCheck['matched_fields']),
                    'matched_villager_id' => VillagerRecord::where('unique_id', $duplicateCheck['matched_villager']['unique_id'])->value('id'),
                    'resolution' => 'pending',
                ]);

                throw new \App\Exceptions\DuplicateDetectedException(
                    'Potential duplicate detected',
                    $duplicateCheck
                );
            }

            // Generate unique ID
            $uniqueId = $this->generateUniqueId();

            // Create the villager record
            $villager = VillagerRecord::create([
                'unique_id' => $uniqueId,
                'full_name' => $data['full_name'],
                'date_of_birth' => $data['date_of_birth'],
                'gender' => $data['gender'],
                'household_id' => $data['household_id'],
                'village' => $data['village'] ?? null,
                'ward' => $data['ward'] ?? null,
                'zone' => $data['zone'] ?? null,
                'passport_photo' => $data['passport_photo'] ?? null,
                'nin' => $data['nin'] ?? null,
                'phone_number' => $data['phone_number'] ?? null,
                'email' => $data['email'] ?? null,
                'bank_account_number' => $data['bank_account_number'] ?? null,
                'bank_name' => $data['bank_name'] ?? null,
                'marital_status' => $data['marital_status'] ?? null,
                'occupation' => $data['occupation'] ?? null,
                'education_level' => $data['education_level'] ?? null,
                'health_status' => $data['health_status'] ?? null,
                'nin_verification_status' => !empty($data['nin']) ? 'pending' : 'not_submitted',
                'status' => 'active',
                'registered_by' => $adminId,
            ]);

            // Log the registration
            $this->auditLogger->log('registration', $adminId, [
                'villager_id' => $uniqueId,
                'description' => "Registered villager: {$data['full_name']}",
                'metadata' => ['unique_id' => $uniqueId],
            ]);

            // Queue NIN verification if NIN provided
            if (!empty($data['nin'])) {
                dispatch(new \App\Jobs\VerifyNinJob($villager->id));
            }

            return $villager;
        });
    }

    public function update(string $uniqueId, array $data, int $adminId): VillagerRecord
    {
        return DB::transaction(function () use ($uniqueId, $data, $adminId) {
            $villager = VillagerRecord::where('unique_id', $uniqueId)->first();

            if (!$villager) {
                throw new \App\Exceptions\RecordNotFoundException(
                    "Villager record not found for ID: {$uniqueId}"
                );
            }

            // Check duplicates if sensitive fields changed
            $sensitiveFields = ['phone_number', 'bank_account_number', 'nin'];
            $changedSensitive = [];
            foreach ($sensitiveFields as $field) {
                if (isset($data[$field]) && $data[$field] !== $villager->$field) {
                    $changedSensitive[$field] = $data[$field];
                }
            }

            if (!empty($changedSensitive)) {
                $duplicateCheck = $this->fraudDetector->checkDuplicates($changedSensitive, $uniqueId);
                if ($duplicateCheck['has_duplicate']) {
                    throw new \App\Exceptions\DuplicateDetectedException(
                        'Duplicate detected during update',
                        $duplicateCheck
                    );
                }
            }

            // Track history for changed fields
            $updatableFields = ['full_name', 'date_of_birth', 'gender', 'household_id', 'phone_number', 'bank_account_number', 'nin'];
            $changedFields = [];

            foreach ($updatableFields as $field) {
                if (isset($data[$field]) && $data[$field] != $villager->$field) {
                    $changedFields[$field] = [
                        'old' => $villager->$field,
                        'new' => $data[$field],
                    ];

                    \App\Models\VillagerHistory::create([
                        'villager_record_id' => $villager->id,
                        'field_name' => $field,
                        'old_value' => $villager->$field,
                        'new_value' => $data[$field],
                        'changed_by' => $adminId,
                        'created_at' => now(),
                    ]);
                }
            }

            // Apply updates
            $villager->update(array_intersect_key($data, array_flip($updatableFields)));

            // Audit log
            $this->auditLogger->log('update', $adminId, [
                'villager_id' => $uniqueId,
                'description' => "Updated villager: {$villager->full_name}",
                'metadata' => ['changed_fields' => $changedFields],
            ]);

            return $villager->fresh();
        });
    }

    public function findByUniqueId(string $uniqueId): ?VillagerRecord
    {
        return VillagerRecord::where('unique_id', $uniqueId)->first();
    }

    private function generateUniqueId(): string
    {
        $date = now()->format('Ymd');
        $lastRecord = VillagerRecord::where('unique_id', 'like', "ALC-{$date}-%")
            ->orderBy('unique_id', 'desc')
            ->first();

        if ($lastRecord) {
            $lastSequence = (int) substr($lastRecord->unique_id, -5);
            $nextSequence = $lastSequence + 1;
        } else {
            $nextSequence = 1;
        }

        return sprintf('ALC-%s-%05d', $date, $nextSequence);
    }
}
