<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    public function up(): void
    {
        Schema::table('villager_records', function (Blueprint $table) {
            $table->string('village')->nullable()->after('household_id');
            $table->string('ward')->nullable()->after('village');
            $table->string('zone')->nullable()->after('ward');
        });
    }

    public function down(): void
    {
        Schema::table('villager_records', function (Blueprint $table) {
            $table->dropColumn(['village', 'ward', 'zone']);
        });
    }
};
