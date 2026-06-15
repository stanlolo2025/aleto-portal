<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    public function up(): void
    {
        Schema::create('student_registry', function (Blueprint $table) {
            $table->id();
            $table->string('student_id')->unique();
            $table->foreignId('villager_record_id')->constrained('villager_records')->onDelete('cascade');
            $table->string('school_name');
            $table->string('class_level');
            $table->enum('enrollment_status', ['enrolled', 'not_enrolled', 'dropped_out', 'graduated'])->default('enrolled');
            $table->integer('days_present')->default(0);
            $table->integer('days_absent')->default(0);
            $table->foreignId('registered_by')->constrained('users')->onDelete('cascade');
            $table->timestamps();

            $table->index('villager_record_id');
            $table->index('enrollment_status');
        });
    }

    public function down(): void
    {
        Schema::dropIfExists('student_registry');
    }
};
