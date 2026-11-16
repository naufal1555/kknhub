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
        Schema::create('kkn_teams', function (Blueprint $table) {
            $table->id();
            $table->string('name');
            $table->string('period')->nullable();
            $table->string('target_location');
            $table->text('description')->nullable();
            $table->foreignId('pic_user_id')->nullable()->constrained('users')->nullOnDelete();
            $table->timestamps();
        });

        Schema::create('contact_persons', function (Blueprint $table) {
            $table->id();
            $table->foreignId('team_id')->constrained('kkn_teams')->cascadeOnDelete();
            $table->string('name');
            $table->string('role_title')->nullable();
            $table->string('phone_number');
            $table->string('line_id')->nullable();
            $table->timestamps();
        });

        Schema::create('pic_registrations', function (Blueprint $table) {
            $table->id();
            $table->foreignId('user_id')->constrained('users')->cascadeOnDelete();
            $table->foreignId('team_id')->nullable()->constrained('kkn_teams')->nullOnDelete();
            $table->string('proposed_team_name');
            $table->string('target_location');
            $table->string('period')->nullable();
            $table->text('description')->nullable();
            $table->enum('status', ['pending', 'approved', 'rejected'])->default('pending');
            $table->text('rejection_reason')->nullable();
            $table->foreignId('verified_by_admin_id')->nullable()->constrained('users')->nullOnDelete();
            $table->timestamps();
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::dropIfExists('pic_registrations');
        Schema::dropIfExists('contact_persons');
        Schema::dropIfExists('kkn_teams');
    }
};
