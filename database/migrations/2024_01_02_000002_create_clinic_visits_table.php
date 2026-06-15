<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    public function up(): void
    {
        Schema::create('clinic_visits', function (Blueprint $table) {
            $table->id();
            $table->string('visit_id')->unique();
            $table->foreignId('villager_record_id')->constrained('villager_records')->onDelete('cascade');
            $table->string('clinic_name');
            $table->string('clinic_location')->nullable();
            $table->date('visit_date');
            $table->enum('reason', ['diagnosis', 'check_up', 'emergency', 'follow_up', 'vaccination', 'other']);
            $table->text('treatment')->nullable();
            $table->string('health_worker');
            $table->text('notes')->nullable();
            $table->foreignId('recorded_by')->constrained('users')->onDelete('cascade');
            $table->timestamps();

            $table->index('villager_record_id');
            $table->index('visit_date');
        });
    }

    public function down(): void
    {
        Schema::dropIfExists('clinic_visits');
    }
};
