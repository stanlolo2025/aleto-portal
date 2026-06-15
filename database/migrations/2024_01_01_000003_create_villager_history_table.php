<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    public function up(): void
    {
        Schema::create('villager_history', function (Blueprint $table) {
            $table->id();
            $table->foreignId('villager_record_id')->constrained('villager_records')->onDelete('cascade');
            $table->string('field_name');
            $table->text('old_value')->nullable();
            $table->text('new_value')->nullable();
            $table->foreignId('changed_by')->constrained('users')->onDelete('cascade');
            $table->timestamp('created_at');

            $table->index('villager_record_id');
        });
    }

    public function down(): void
    {
        Schema::dropIfExists('villager_history');
    }
};
