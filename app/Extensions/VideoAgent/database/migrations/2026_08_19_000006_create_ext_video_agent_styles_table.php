<?php

declare(strict_types=1);

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    public function up(): void
    {
        Schema::create('ext_video_agent_styles', function (Blueprint $table) {
            $table->id();
            $table->string('heygen_id')->unique();
            $table->string('name')->nullable();
            $table->text('thumbnail_url')->nullable();
            $table->text('preview_video_url')->nullable();
            $table->string('aspect_ratio')->nullable();
            $table->json('tags')->nullable();
            $table->json('payload')->nullable();
            $table->timestamp('synced_at')->nullable();
            $table->timestamps();
        });
    }

    public function down(): void
    {
        Schema::dropIfExists('ext_video_agent_styles');
    }
};
