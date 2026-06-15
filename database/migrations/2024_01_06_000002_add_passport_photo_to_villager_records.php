<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    public function up(): void
    {
        Schema::table('villager_records', function (Blueprint $table) {
            $table->string('passport_photo')->nullable()->after('zone');
        });
    }

    public function down(): void
    {
        Schema::table('villager_records', function (Blueprint $table) {
            $table->dropColumn('passport_photo');
        });
    }
};
