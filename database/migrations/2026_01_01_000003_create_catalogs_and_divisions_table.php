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
        Schema::create('oprec_catalogs', function (Blueprint $table) {
            $table->id();
            $table->foreignId('team_id')->constrained('kkn_teams')->cascadeOnDelete();
            $table->string('title');
            $table->text('description')->nullable();
            $table->dateTime('start_date')->nullable();
            $table->dateTime('deadline');
            $table->enum('status', ['draft', 'pending_approval', 'published', 'rejected', 'closed'])->default('draft');
            $table->text('rejection_note')->nullable();
            $table->timestamps();

            $table->index(['status', 'deadline']);
        });

        Schema::create('divisions', function (Blueprint $table) {
            $table->id();
            $table->foreignId('catalog_id')->constrained('oprec_catalogs')->cascadeOnDelete();
            $table->string('name');
            $table->text('job_description')->nullable();
            $table->text('requirements')->nullable();
            $table->unsignedInteger('quota');
            $table->timestamps();
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::dropIfExists('divisions');
        Schema::dropIfExists('oprec_catalogs');
    }
};
