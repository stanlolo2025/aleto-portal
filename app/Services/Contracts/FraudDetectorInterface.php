<?php

namespace App\Services\Contracts;

interface FraudDetectorInterface
{
    /**
     * Check for duplicate records based on phone, bank account, or NIN.
     * Returns array with: has_duplicate, matched_fields, matched_villager
     */
    public function checkDuplicates(array $fields, ?string $excludeId = null): array;

    /**
     * Compare fingerprint against all stored biometric data.
     * Returns array with: has_match, similarity_score, matched_villager
     */
    public function compareBiometric(string $fingerprintData, ?string $excludeId = null): array;

    /**
     * Verify identity of an existing villager.
     * Returns array with: match, similarity_score
     */
    public function verifyIdentity(string $uniqueId, string $fingerprintData): array;
}
