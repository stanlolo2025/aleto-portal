<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    public function up(): void
    {
        Schema::create('flagged_registrations', function (Blueprint $table) {
            $table->id();
            $table->json('submitted_data');
            $table->string('matched_field'); // phone_number|bank_account_number|nin
            $table->foreignId('matched_villager_id')->nullable()->constrained('villager_records')->onDelete('set null');
            $table->enum('resolution', ['pending', 'confirmed_duplicate', 'confirmed_not_duplicate'])->default('pending');
            $table->text('justification')->nullable();
            $table->foreignId('resolved_by')->nullable()->constrained('users')->onDelete('set null');
            $table->timestamp('resolved_at')->nullable();
            $table->timestamps();

            $table->index('resolution');
        });
    }

    public function down(): void
    {
        Schema::dropIfExists('flagged_registrations');
    }
};
