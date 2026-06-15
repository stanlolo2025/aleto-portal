<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    public function up(): void
    {
        Schema::create('villager_records', function (Blueprint $table) {
            $table->id();
            $table->string('unique_id')->unique();
            $table->string('full_name');
            $table->date('date_of_birth');
            $table->enum('gender', ['male', 'female', 'other']);
            $table->string('household_id');
            $table->text('nin')->nullable(); // encrypted - National ID Number
            $table->text('phone_number')->nullable(); // encrypted
            $table->string('email')->nullable();
            $table->text('bank_account_number')->nullable(); // encrypted
            $table->string('bank_name')->nullable();
            $table->enum('marital_status', ['single', 'married', 'widowed', 'divorced'])->nullable();
            $table->string('occupation')->nullable();
            $table->enum('education_level', ['none', 'primary', 'secondary', 'tertiary'])->nullable();
            $table->text('health_status')->nullable();
            $table->enum('nin_verification_status', ['pending', 'verified', 'unverified', 'not_submitted'])->default('not_submitted');
            $table->enum('status', ['active', 'deceased', 'archived'])->default('active');
            $table->date('date_of_death')->nullable();
            $table->string('archive_reason', 500)->nullable();
            $table->foreignId('registered_by')->nullable()->constrained('users')->onDelete('set null');
            $table->timestamps();

            $table->index('status');
            $table->index('household_id');
        });
    }

    public function down(): void
    {
        Schema::dropIfExists('villager_records');
    }
};
