<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    public function up(): void
    {
        Schema::create('health_grants', function (Blueprint $table) {
            $table->id();
            $table->foreignId('grant_id')->nullable()->constrained('grants')->onDelete('set null');
            $table->foreignId('villager_record_id')->constrained('villager_records')->onDelete('cascade');
            $table->enum('grant_type', ['medical_subsidy', 'elderly_health_stipend', 'disability_support', 'maternal_care', 'other']);
            $table->decimal('amount', 11, 2);
            $table->enum('payment_method', ['bank_transfer', 'proxy_account', 'mobile_wallet']);
            $table->enum('approval_status', ['pending', 'approved', 'paid', 'rejected'])->default('pending');
            $table->foreignId('approved_by')->nullable()->constrained('users')->onDelete('set null');
            $table->timestamp('approved_at')->nullable();
            $table->text('remarks')->nullable();
            $table->foreignId('created_by')->constrained('users')->onDelete('cascade');
            $table->timestamps();

            $table->index('villager_record_id');
            $table->index('approval_status');
        });
    }

    public function down(): void
    {
        Schema::dropIfExists('health_grants');
    }
};
