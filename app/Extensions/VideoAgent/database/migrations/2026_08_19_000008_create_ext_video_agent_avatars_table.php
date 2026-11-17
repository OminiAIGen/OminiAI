<?php

declare(strict_types=1);

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    public function up(): void
    {
        Schema::create('ext_video_agent_avatars', function (Blueprint $table) {
            $table->id();
            $table->string('heygen_id')->unique();
            $table->string('group_id')->nullable()->index();
            $table->string('name')->nullable();
            $table->string('avatar_type')->nullable();
            $table->string('ownership')->default('public');
            $table->string('gender')->nullable();
            $table->string('status')->nullable();
            $table->text('preview_image_url')->nullable();
            $table->text('preview_video_url')->nullable();
            $table->string('default_voice_id')->nullable();
            $table->string('preferred_orientation')->nullable();
            $table->json('supported_api_engines')->nullable();
            $table->json('tags')->nullable();
            $table->json('payload')->nullable();
            $table->timestamp('synced_at')->nullable();
            $table->timestamps();

            $table->index(['ownership', 'avatar_type']);
        });
    }

    public function down(): void
    {
        Schema::dropIfExists('ext_video_agent_avatars');
    }
};
