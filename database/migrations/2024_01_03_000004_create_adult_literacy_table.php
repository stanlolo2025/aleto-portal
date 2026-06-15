<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    public function up(): void
    {
        Schema::create('adult_literacy', function (Blueprint $table) {
            $table->id();
            $table->string('program_id')->unique();
            $table->foreignId('villager_record_id')->constrained('villager_records')->onDelete('cascade');
            $table->string('program_name');
            $table->enum('enrollment_status', ['enrolled', 'completed', 'dropped_out'])->default('enrolled');
            $table->integer('sessions_attended')->default(0);
            $table->integer('total_sessions')->default(0);
            $table->enum('completion_status', ['in_progress', 'completed', 'incomplete'])->default('in_progress');
            $table->date('start_date');
            $table->date('end_date')->nullable();
            $table->foreignId('registered_by')->constrained('users')->onDelete('cascade');
            $table->timestamps();

            $table->index('villager_record_id');
        });
    }

    public function down(): void
    {
        Schema::dropIfExists('adult_literacy');
    }
};
