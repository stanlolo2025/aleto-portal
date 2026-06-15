<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    public function up(): void
    {
        Schema::create('biometric_data', function (Blueprint $table) {
            $table->id();
            $table->foreignId('villager_record_id')->constrained('villager_records')->onDelete('cascade');
            $table->longText('fingerprint_template'); // encrypted blob
            $table->text('facial_photo_path'); // encrypted path
            $table->timestamp('captured_at');
            $table->timestamps();

            $table->unique('villager_record_id');
        });
    }

    public function down(): void
    {
        Schema::dropIfExists('biometric_data');
    }
};
