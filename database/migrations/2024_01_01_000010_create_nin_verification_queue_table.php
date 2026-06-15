<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    public function up(): void
    {
        Schema::create('nin_verification_queue', function (Blueprint $table) {
            $table->id();
            $table->foreignId('villager_record_id')->constrained('villager_records')->onDelete('cascade');
            $table->text('nin'); // encrypted
            $table->integer('attempts')->default(0);
            $table->enum('status', ['pending', 'success', 'failed', 'mismatch'])->default('pending');
            $table->text('response_data')->nullable();
            $table->timestamp('next_retry_at')->nullable();
            $table->timestamps();

            $table->index('status');
            $table->index('next_retry_at');
        });
    }

    public function down(): void
    {
        Schema::dropIfExists('nin_verification_queue');
    }
};
