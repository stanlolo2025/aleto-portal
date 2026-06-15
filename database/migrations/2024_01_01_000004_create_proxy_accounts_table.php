<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    public function up(): void
    {
        Schema::create('proxy_accounts', function (Blueprint $table) {
            $table->id();
            $table->foreignId('villager_record_id')->unique()->constrained('villager_records')->onDelete('cascade');
            $table->string('representative_name');
            $table->enum('relationship', ['spouse', 'child', 'sibling', 'parent', 'grandchild', 'legal_guardian']);
            $table->string('proxy_bank_name');
            $table->text('proxy_bank_account'); // encrypted
            $table->timestamps();

            $table->index('representative_name');
        });
    }

    public function down(): void
    {
        Schema::dropIfExists('proxy_accounts');
    }
};
