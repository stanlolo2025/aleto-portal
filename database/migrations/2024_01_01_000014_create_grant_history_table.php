<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    public function up(): void
    {
        Schema::create('grant_history', function (Blueprint $table) {
            $table->id();
            $table->string('history_id')->unique(); // Auto-generated alphanumeric
            $table->foreignId('grant_id')->constrained('grants')->onDelete('cascade');
            $table->foreignId('villager_record_id')->nullable()->constrained('villager_records')->onDelete('set null');
            $table->enum('action_type', ['registration', 'approval', 'payment', 'update', 'cancellation']);
            $table->timestamp('action_date');
            $table->decimal('amount', 11, 2)->nullable();
            $table->enum('payment_method', ['bank_transfer', 'proxy_account', 'mobile_wallet'])->nullable();
            $table->string('transaction_reference')->nullable();
            $table->foreignId('performed_by')->constrained('users')->onDelete('cascade');
            $table->text('remarks')->nullable();
            $table->timestamps();

            $table->index('grant_id');
            $table->index('villager_record_id');
            $table->index('action_type');
            $table->index('action_date');
        });
    }

    public function down(): void
    {
        Schema::dropIfExists('grant_history');
    }
};
