<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    public function up(): void
    {
        Schema::create('preventive_care_alerts', function (Blueprint $table) {
            $table->id();
            $table->string('alert_id')->unique();
            $table->foreignId('villager_record_id')->constrained('villager_records')->onDelete('cascade');
            $table->enum('alert_type', ['immunization', 'maternal', 'chronic', 'follow_up', 'other']);
            $table->string('description');
            $table->date('due_date');
            $table->enum('status', ['pending', 'completed', 'overdue', 'cancelled'])->default('pending');
            $table->date('completed_date')->nullable();
            $table->foreignId('created_by')->constrained('users')->onDelete('cascade');
            $table->timestamps();

            $table->index('villager_record_id');
            $table->index('due_date');
            $table->index('status');
        });
    }

    public function down(): void
    {
        Schema::dropIfExists('preventive_care_alerts');
    }
};
