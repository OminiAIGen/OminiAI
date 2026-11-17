<?php

declare(strict_types=1);

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    public function up(): void
    {
        Schema::create('ext_video_agent_sessions', function (Blueprint $table) {
            $table->id();
            $table->uuid('uuid')->unique();
            $table->foreignId('user_id')->constrained()->cascadeOnDelete();
            $table->unsignedBigInteger('team_id')->nullable();
            $table->string('heygen_session_id')->nullable()->index();
            $table->string('mode')->default('chat');
            $table->text('prompt');
            $table->string('title')->nullable();
            $table->string('style_id')->nullable();
            $table->string('brand_kit_id')->nullable();
            $table->string('avatar_id')->nullable();
            $table->string('voice_id')->nullable();
            $table->string('orientation')->nullable();
            $table->string('resolution')->nullable();
            $table->string('status')->default('thinking');
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
        Schema::dropIfExists('ext_video_agent_sessions');
    }
};
