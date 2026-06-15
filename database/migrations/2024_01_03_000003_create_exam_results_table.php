<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    public function up(): void
    {
        Schema::create('exam_results', function (Blueprint $table) {
            $table->id();
            $table->string('exam_id')->unique();
            $table->foreignId('student_id')->constrained('student_registry')->onDelete('cascade');
            $table->string('subject');
            $table->string('score');
            $table->string('grade')->nullable();
            $table->date('exam_date');
            $table->string('exam_type')->nullable(); // mid-term, final, external
            $table->text('remarks')->nullable();
            $table->foreignId('recorded_by')->constrained('users')->onDelete('cascade');
            $table->timestamps();

            $table->index('student_id');
        });
    }

    public function down(): void
    {
        Schema::dropIfExists('exam_results');
    }
};
