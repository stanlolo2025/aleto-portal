<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    public function up(): void
    {
        Schema::create('medical_records', function (Blueprint $table) {
            $table->id();
            $table->string('record_id')->unique();
            $table->foreignId('villager_record_id')->constrained('villager_records')->onDelete('cascade');
            $table->string('vaccination_type')->nullable();
            $table->date('vaccination_date')->nullable();
            $table->enum('vaccination_status', ['completed', 'partial', 'pending'])->nullable();
            $table->text('chronic_conditions')->nullable();
            $table->boolean('disability_status')->default(false);
            $table->text('allergies')->nullable();
            $table->text('notes')->nullable();
            $table->foreignId('recorded_by')->constrained('users')->onDelete('cascade');
            $table->timestamps();

            $table->index('villager_record_id');
        });
    }

    public function down(): void
    {
        Schema::dropIfExists('medical_records');
    }
};
