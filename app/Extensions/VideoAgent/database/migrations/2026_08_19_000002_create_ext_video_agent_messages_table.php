<?php

declare(strict_types=1);

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    public function up(): void
    {
        Schema::create('ext_video_agent_messages', function (Blueprint $table) {
            $table->id();
            $table->foreignId('session_id')->constrained('ext_video_agent_sessions')->cascadeOnDelete();
            $table->string('role')->default('agent');
            $table->string('kind')->default('message');
            $table->longText('body')->nullable();
            $table->string('heygen_message_id')->nullable();
            $table->string('resource_id')->nullable();
            $table->json('meta')->nullable();
            $table->timestamps();

            $table->index(['session_id', 'created_at']);
            $table->unique(['session_id', 'heygen_message_id']);
        });
    }

    public function down(): void
    {
        Schema::dropIfExists('ext_video_agent_messages');
    }
};
