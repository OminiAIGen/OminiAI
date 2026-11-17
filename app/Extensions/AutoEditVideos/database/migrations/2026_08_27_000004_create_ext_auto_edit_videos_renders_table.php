<?php

declare(strict_types=1);

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    public function up(): void
    {
        Schema::create('ext_auto_edit_videos_renders', function (Blueprint $table) {
            $table->id();
            $table->uuid('uuid')->unique();
            $table->foreignId('session_id')->constrained('ext_auto_edit_videos_sessions')->cascadeOnDelete();
            $table->foreignId('user_id')->constrained()->cascadeOnDelete();
            $table->unsignedBigInteger('team_id')->nullable();
            $table->string('kind')->default('auto_edit');
            $table->string('rendley_job_id')->nullable()->index();
            $table->string('title')->nullable();
            $table->string('status')->default('queued');
            $table->string('target_resolution')->nullable();
            $table->unsignedInteger('duration_seconds')->nullable();
            $table->text('remote_url')->nullable();
            $table->text('thumbnail_url')->nullable();
            $table->string('local_path')->nullable();
            $table->decimal('rendley_credits', 12, 4)->nullable();
            $table->decimal('credits', 12, 4)->nullable();
            $table->boolean('usage_deducted')->default(false);
            $table->unsignedInteger('poll_attempts')->default(0);
            $table->timestamp('last_polled_at')->nullable();
            $table->text('error_message')->nullable();
            $table->timestamp('completed_at')->nullable();
            $table->json('payload')->nullable();
            $table->timestamps();

            $table->index(['user_id', 'status']);
            $table->index(['session_id', 'status']);
        });
    }

    public function down(): void
    {
        Schema::dropIfExists('ext_auto_edit_videos_renders');
    }
};
