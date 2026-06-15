<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    public function up(): void
    {
        Schema::create('scholarships', function (Blueprint $table) {
            $table->id();
            $table->string('scholarship_id')->unique();
            $table->foreignId('student_id')->constrained('student_registry')->onDelete('cascade');
            $table->enum('scholarship_type', ['government_bursary', 'community_grant', 'ngo_sponsorship', 'other']);
            $table->decimal('amount', 11, 2);
            $table->enum('payment_method', ['bank_transfer', 'proxy_account', 'mobile_wallet']);
            $table->enum('approval_status', ['pending', 'approved', 'paid', 'rejected'])->default('pending');
            $table->foreignId('approved_by')->nullable()->constrained('users')->onDelete('set null');
            $table->timestamp('approved_at')->nullable();
            $table->string('academic_year')->nullable();
            $table->text('remarks')->nullable();
            $table->foreignId('created_by')->constrained('users')->onDelete('cascade');
            $table->timestamps();

            $table->index('student_id');
            $table->index('approval_status');
        });
    }

    public function down(): void
    {
        Schema::dropIfExists('scholarships');
    }
};
