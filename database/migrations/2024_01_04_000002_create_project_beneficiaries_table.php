<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    public function up(): void
    {
        Schema::create('project_beneficiaries', function (Blueprint $table) {
            $table->id();
            $table->string('mapping_id')->unique();
            $table->foreignId('project_id')->constrained('projects')->onDelete('cascade');
            $table->foreignId('villager_record_id')->constrained('villager_records')->onDelete('cascade');
            $table->string('benefit_type'); // water access, electricity, road usage
            $table->text('remarks')->nullable();
            $table->foreignId('recorded_by')->constrained('users')->onDelete('cascade');
            $table->timestamps();

            $table->index('project_id');
            $table->index('villager_record_id');
            $table->unique(['project_id', 'villager_record_id'], 'proj_villager_unique');
        });
    }

    public function down(): void
    {
        Schema::dropIfExists('project_beneficiaries');
    }
};
