<?php

namespace App\Services;

use App\Models\BiometricData;
use App\Models\VillagerRecord;
use App\Services\Contracts\FraudDetectorInterface;
use Illuminate\Support\Facades\Crypt;
use Illuminate\Support\Facades\DB;

class FraudDetectorService implements FraudDetectorInterface
{
    public function checkDuplicates(array $fields, ?string $excludeId = null): array
    {
        $matchedFields = [];
        $matchedVillager = null;

        $records = VillagerRecord::query()
            ->when($excludeId, fn($q) => $q->where('unique_id', '!=', $excludeId))
            ->get(['id', 'unique_id', 'full_name', 'phone_number', 'bank_account_number', 'nin', 'status']);

        foreach ($records as $record) {
            if (!empty($fields['phone_number']) && $record->phone_number === $fields['phone_number']) {
                $matchedFields[] = 'phone_number';
                $matchedVillager = $record;
            }
            if (!empty($fields['bank_account_number']) && $record->bank_account_number === $fields['bank_account_number']) {
                $matchedFields[] = 'bank_account_number';
                $matchedVillager = $record;
            }
            if (!empty($fields['nin']) && $record->nin === $fields['nin']) {
                $matchedFields[] = 'nin';
                $matchedVillager = $record;
            }

            if (!empty($matchedFields)) {
                break; // Found a match, stop searching
            }
        }

        return [
            'has_duplicate' => !empty($matchedFields),
            'matched_fields' => $matchedFields,
            'matched_villager' => $matchedVillager ? [
                'unique_id' => $matchedVillager->unique_id,
                'full_name' => $matchedVillager->full_name,
                'status' => $matchedVillager->status,
                'matched_values' => array_intersect_key(
                    $fields,
                    array_flip($matchedFields)
                ),
            ] : null,
        ];
    }

    public function compareBiometric(string $fingerprintData, ?string $excludeId = null): array
    {
        // In production, this would use a biometric SDK for fingerprint comparison.
        // For now, we implement a placeholder that can be replaced with actual SDK integration.
        $biometrics = BiometricData::query()
            ->when($excludeId, function ($q) use ($excludeId) {
                $q->whereHas('villagerRecord', fn($vq) => $vq->where('unique_id', '!=', $excludeId));
            })
            ->with('villagerRecord:id,unique_id,full_name,status')
            ->get();

        foreach ($biometrics as $biometric) {
            // Placeholder: In real implementation, use biometric SDK to compare
            // fingerprint templates and return similarity score
            $similarityScore = $this->calculateFingerprintSimilarity(
                $fingerprintData,
                $biometric->fingerprint_template
            );

            if ($similarityScore >= 0.95) {
                return [
                    'has_match' => true,
                    'similarity_score' => $similarityScore,
                    'matched_villager' => [
                        'unique_id' => $biometric->villagerRecord->unique_id,
                        'full_name' => $biometric->villagerRecord->full_name,
                        'status' => $biometric->villagerRecord->status,
                    ],
                ];
            }
        }

        return [
            'has_match' => false,
            'similarity_score' => 0,
            'matched_villager' => null,
        ];
    }

    public function verifyIdentity(string $uniqueId, string $fingerprintData): array
    {
        $villager = VillagerRecord::where('unique_id', $uniqueId)->first();

        if (!$villager || !$villager->biometricData) {
            return ['match' => false, 'similarity_score' => 0, 'error' => 'No biometric data found'];
        }

        $similarityScore = $this->calculateFingerprintSimilarity(
            $fingerprintData,
            $villager->biometricData->fingerprint_template
        );

        return [
            'match' => $similarityScore >= 0.95,
            'similarity_score' => $similarityScore,
        ];
    }

    /**
     * Placeholder fingerprint comparison.
     * Replace with actual biometric SDK integration (e.g., SourceAFIS, SecuGen).
     */
    private function calculateFingerprintSimilarity(string $template1, string $template2): float
    {
        if ($template1 === $template2) {
            return 1.0;
        }

        // In production, this would call biometric matching library
        // For development, return a low score for different templates
        return 0.0;
    }
}
