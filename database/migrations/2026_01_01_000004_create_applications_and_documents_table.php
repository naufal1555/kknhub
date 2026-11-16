<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    /**
     * Run the migrations.
     */
    public function up(): void
    {
        Schema::create('applications', function (Blueprint $table) {
            $table->id();
            $table->foreignId('user_id')->constrained('users')->cascadeOnDelete();
            $table->foreignId('team_id')->constrained('kkn_teams')->cascadeOnDelete();
            $table->foreignId('division_id')->constrained('divisions')->cascadeOnDelete();
            $table->enum('status', ['pending', 'accepted', 'rejected', 'cancelled'])->default('pending');
            $table->dateTime('submitted_at');
            $table->dateTime('decided_at')->nullable();
            $table->text('notes')->nullable();
            $table->timestamps();

            // Aturan Bisnis PRD: Satu mahasiswa hanya boleh mengajukan satu kali ke tim yang sama
            $table->unique(['user_id', 'team_id'], 'unique_user_team_application');
            $table->index(['user_id', 'status'], 'idx_user_application_status');
            $table->index(['division_id', 'status'], 'idx_division_status');
        });

        Schema::create('application_documents', function (Blueprint $table) {
            $table->id();
            $table->foreignId('application_id')->constrained('applications')->cascadeOnDelete();
            $table->enum('document_type', ['cv', 'portfolio']);
            $table->string('s3_key');
            $table->string('original_filename');
            $table->unsignedBigInteger('file_size_bytes');
            $table->string('mime_type')->default('application/pdf');
            $table->timestamps();
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::dropIfExists('application_documents');
        Schema::dropIfExists('applications');
    }
};
