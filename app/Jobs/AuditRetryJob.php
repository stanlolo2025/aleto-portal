<?php

namespace App\Jobs;

use App\Models\AuditLog;
use Illuminate\Bus\Queueable;
use Illuminate\Contracts\Queue\ShouldQueue;
use Illuminate\Foundation\Bus\Dispatchable;
use Illuminate\Queue\InteractsWithQueue;
use Illuminate\Queue\SerializesModels;

class AuditRetryJob implements ShouldQueue
{
    use Dispatchable, InteractsWithQueue, Queueable, SerializesModels;

    public int $tries = 5;
    public int $backoff = 60; // 1 minute

    public function __construct(
        private string $eventType,
        private ?int $userId,
        private array $details,
    ) {}

    public function handle(): void
    {
        AuditLog::create([
            'event_type' => $this->eventType,
            'event_timestamp' => now(),
            'user_id' => $this->userId,
            'ip_address' => $this->details['ip_address'] ?? null,
            'affected_villager_id' => $this->details['villager_id'] ?? null,
            'description' => $this->details['description'] ?? '',
            'metadata' => $this->details['metadata'] ?? null,
            'created_at' => now(),
        ]);
    }
}
