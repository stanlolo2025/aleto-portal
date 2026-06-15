<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    public function up(): void
    {
        Schema::create('impact_analytics', function (Blueprint $table) {
            $table->id();
            $table->string('impact_id')->unique();
            $table->foreignId('project_id')->constrained('projects')->onDelete('cascade');
            $table->string('metric'); // e.g. % households with water
            $table->decimal('value', 10, 2);
            $table->date('date_recorded');
            $table->text('notes')->nullable();
            $table->foreignId('recorded_by')->constrained('users')->onDelete('cascade');
            $table->timestamps();

            $table->index('project_id');
        });
    }

    public function down(): void
    {
        Schema::dropIfExists('impact_analytics');
    }
};
