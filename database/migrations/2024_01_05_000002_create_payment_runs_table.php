<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    public function up(): void
    {
        Schema::create('payment_runs', function (Blueprint $table) {
            $table->id();
            $table->string('run_id')->unique();
            $table->foreignId('beneficiary_list_id')->constrained('beneficiary_lists')->onDelete('cascade');
            $table->enum('status', ['pending', 'in_progress', 'completed', 'failed'])->default('pending');
            $table->integer('total_beneficiaries')->default(0);
            $table->integer('paid_count')->default(0);
            $table->integer('failed_count')->default(0);
            $table->decimal('total_amount', 15, 2)->default(0);
            $table->decimal('paid_amount', 15, 2)->default(0);
            $table->foreignId('initiated_by')->constrained('users')->onDelete('cascade');
            $table->timestamp('completed_at')->nullable();
            $table->timestamps();
        });

        Schema::create('payment_run_items', function (Blueprint $table) {
            $table->id();
            $table->foreignId('payment_run_id')->constrained('payment_runs')->onDelete('cascade');
            $table->foreignId('villager_record_id')->constrained('villager_records')->onDelete('cascade');
            $table->decimal('amount', 11, 2);
            $table->enum('status', ['pending', 'paid', 'failed'])->default('pending');
            $table->string('bank_name')->nullable();
            $table->string('account_number')->nullable();
            $table->string('transaction_reference')->nullable();
            $table->timestamp('paid_at')->nullable();
            $table->text('failure_reason')->nullable();
            $table->foreignId('confirmed_by')->nullable()->constrained('users')->onDelete('set null');
            $table->timestamps();

            $table->index('payment_run_id');
            $table->index('status');
        });
    }

    public function down(): void
    {
        Schema::dropIfExists('payment_run_items');
        Schema::dropIfExists('payment_runs');
    }
};
