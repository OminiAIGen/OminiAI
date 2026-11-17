<?php

declare(strict_types=1);

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    public function up(): void
    {
        Schema::create('ext_video_agent_voices', function (Blueprint $table) {
            $table->id();
            $table->string('heygen_id')->unique();
            $table->string('name')->nullable();
            $table->string('language')->nullable();
            $table->string('gender')->nullable();
            $table->string('type')->default('public');
            $table->text('preview_audio_url')->nullable();
            $table->boolean('support_pause')->default(false);
            $table->boolean('support_locale')->default(false);
            $table->json('payload')->nullable();
            $table->timestamp('synced_at')->nullable();
            $table->timestamps();

            $table->index(['type', 'language']);
        });
    }

    public function down(): void
    {
        Schema::dropIfExists('ext_video_agent_voices');
    }
};
