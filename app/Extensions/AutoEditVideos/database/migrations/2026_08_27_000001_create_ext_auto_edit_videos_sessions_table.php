<?php

declare(strict_types=1);

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    public function up(): void
    {
        Schema::create('ext_auto_edit_videos_sessions', function (Blueprint $table) {
            $table->id();
            $table->uuid('uuid')->unique();
            $table->foreignId('user_id')->constrained()->cascadeOnDelete();
            $table->unsignedBigInteger('team_id')->nullable();
            $table->string('rendley_project_id')->nullable()->index();
            $table->string('rendley_thread_id')->nullable();
            $table->string('rendley_job_id')->nullable()->index();
            $table->text('prompt');
            $table->string('title')->nullable();
            $table->text('instructions')->nullable();
            $table->unsignedInteger('duration_seconds')->nullable();
            $table->string('target_resolution')->nullable();
            $table->string('aspect_ratio')->nullable();
            $table->string('status')->default('planning');
            $table->text('project_url')->nullable();
            $table->text('error_message')->nullable();
            $table->unsignedInteger('poll_attempts')->default(0);
            $table->timestamp('last_polled_at')->nullable();
            $table->json('meta')->nullable();
            $table->timestamps();

            $table->index(['user_id', 'status']);
        });
    }

    public function down(): void
    {
        Schema::dropIfExists('ext_auto_edit_videos_sessions');
    }
};
