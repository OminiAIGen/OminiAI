<?php

declare(strict_types=1);

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    public function up(): void
    {
        Schema::create('ext_video_agent_resources', function (Blueprint $table) {
            $table->id();
            $table->foreignId('session_id')->constrained('ext_video_agent_sessions')->cascadeOnDelete();
            $table->string('heygen_resource_id');
            $table->string('type')->default('storyboard');
            $table->json('payload')->nullable();
            $table->timestamp('fetched_at')->nullable();
            $table->timestamps();

            $table->unique(['session_id', 'heygen_resource_id']);
        });
    }

    public function down(): void
    {
        Schema::dropIfExists('ext_video_agent_resources');
    }
};
