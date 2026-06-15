<?php

namespace App\Jobs;

use App\Models\NinVerificationQueue;
use App\Models\VillagerRecord;
use Illuminate\Bus\Queueable;
use Illuminate\Contracts\Queue\ShouldQueue;
use Illuminate\Foundation\Bus\Dispatchable;
use Illuminate\Queue\InteractsWithQueue;
use Illuminate\Queue\SerializesModels;
use Illuminate\Support\Facades\Http;
use Illuminate\Support\Facades\Log;

class VerifyNinJob implements ShouldQueue
{
    use Dispatchable, InteractsWithQueue, Queueable, SerializesModels;

    public int $tries = 3;
    public int $backoff = 3600; // 1 hour

    public function __construct(
        private int $villagerRecordId,
    ) {}

    public function handle(): void
    {
        $villager = VillagerRecord::find($this->villagerRecordId);

        if (!$villager || !$villager->nin) {
            return;
        }

        // Get or create queue entry
        $queueEntry = NinVerificationQueue::firstOrCreate(
            ['villager_record_id' => $villager->id],
            ['nin' => $villager->nin, 'status' => 'pending', 'attempts' => 0]
        );

        $queueEntry->increment('attempts');

        try {
            // In production, this would call the actual NIN verification API
            // For now, simulate the verification
            $ninApiUrl = config('services.nin.api_url');

            if (!$ninApiUrl) {
                // API not configured - mark as pending and retry
                throw new \Exception('NIN API not configured');
            }

            $response = Http::timeout(10)->get($ninApiUrl, [
                'nin' => $villager->nin,
            ]);

            if ($response->successful()) {
                $data = $response->json();

                if (!isset($data['name'])) {
                    // NIN not found
                    $queueEntry->update([
                        'status' => 'failed',
                        'response_data' => 'NIN does not exist in the national system',
                    ]);
                    $villager->update(['nin_verification_status' => 'unverified']);
                    return;
                }

                // Compare names
                if (strtolower($data['name']) !== strtolower($villager->full_name)) {
                    $queueEntry->update([
                        'status' => 'mismatch',
                        'response_data' => "Name mismatch: national record shows '{$data['name']}'",
                    ]);
                    $villager->update(['nin_verification_status' => 'unverified']);
                    return;
                }

                // Success
                $queueEntry->update(['status' => 'success']);
                $villager->update(['nin_verification_status' => 'verified']);
            } else {
                throw new \Exception('NIN API returned error: ' . $response->status());
            }
        } catch (\Exception $e) {
            Log::warning("NIN verification failed for villager {$villager->unique_id}: {$e->getMessage()}");

            if ($queueEntry->attempts >= 3) {
                $queueEntry->update(['status' => 'failed', 'response_data' => $e->getMessage()]);
                $villager->update(['nin_verification_status' => 'unverified']);
                // TODO: Notify admin
            } else {
                $queueEntry->update(['next_retry_at' => now()->addHour()]);
                throw $e; // Re-throw to trigger retry
            }
        }
    }
}
