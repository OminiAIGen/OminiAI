<?php

declare(strict_types=1);

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    public function up(): void
    {
        Schema::create('ext_video_agent_videos', function (Blueprint $table) {
            $table->id();
            $table->uuid('uuid')->unique();
            $table->foreignId('user_id')->constrained()->cascadeOnDelete();
            $table->unsignedBigInteger('team_id')->nullable();
            $table->foreignId('session_id')->nullable()->constrained('ext_video_agent_sessions')->nullOnDelete();
            $table->string('kind')->default('agent');
            $table->string('heygen_id')->nullable()->index();
            $table->string('title')->nullable();
            $table->string('status')->default('queued');
            $table->unsignedInteger('duration_seconds')->nullable();
            $table->text('remote_url')->nullable();
            $table->text('thumbnail_url')->nullable();
            $table->string('local_path')->nullable();
            $table->string('storage')->nullable();
            $table->text('heygen_page_url')->nullable();
            $table->text('heygen_share_url')->nullable();
            $table->decimal('credits', 12, 4)->nullable();
            $table->boolean('usage_deducted')->default(false);
            $table->unsignedInteger('poll_attempts')->default(0);
            $table->timestamp('last_polled_at')->nullable();
            $table->json('payload')->nullable();
            $table->text('error_message')->nullable();
            $table->timestamp('completed_at')->nullable();
            $table->timestamps();

            $table->index(['user_id', 'status']);
            $table->index(['user_id', 'kind']);
        });
    }

    public function down(): void
    {
        Schema::dropIfExists('ext_video_agent_videos');
    }
};
