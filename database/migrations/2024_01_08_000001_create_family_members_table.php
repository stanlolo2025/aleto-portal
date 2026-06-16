<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    public function up(): void
    {
        Schema::create('family_members', function (Blueprint $table) {
            $table->id();
            $table->foreignId('villager_record_id')->constrained('villager_records')->onDelete('cascade');
            $table->string('full_name');
            $table->enum('relationship', ['spouse', 'son', 'daughter', 'other']);
            $table->date('date_of_birth')->nullable();
            $table->string('occupation')->nullable();
            $table->timestamps();

            $table->index('villager_record_id');
        });
    }

    public function down(): void
    {
        Schema::dropIfExists('family_members');
    }
};
