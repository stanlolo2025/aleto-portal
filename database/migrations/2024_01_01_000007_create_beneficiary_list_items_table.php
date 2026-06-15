<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    public function up(): void
    {
        Schema::create('beneficiary_list_items', function (Blueprint $table) {
            $table->id();
            $table->foreignId('beneficiary_list_id')->constrained('beneficiary_lists')->onDelete('cascade');
            $table->foreignId('villager_record_id')->constrained('villager_records')->onDelete('cascade');
            $table->decimal('grant_amount', 11, 2);
            $table->boolean('duplicate_flagged')->default(false);
            $table->boolean('reviewed_not_duplicate')->default(false);
            $table->string('review_justification')->nullable();
            $table->timestamp('created_at');

            $table->unique(['beneficiary_list_id', 'villager_record_id'], 'ben_list_villager_unique');
        });
    }

    public function down(): void
    {
        Schema::dropIfExists('beneficiary_list_items');
    }
};
