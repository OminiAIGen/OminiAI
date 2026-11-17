<?php

declare(strict_types=1);

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    public function up(): void
    {
        Schema::create('ext_auto_edit_videos_messages', function (Blueprint $table) {
            $table->id();
            $table->foreignId('session_id')->constrained('ext_auto_edit_videos_sessions')->cascadeOnDelete();
            $table->string('role')->default('agent');
            $table->string('kind')->default('message');
            $table->longText('body')->nullable();
            $table->string('rendley_job_id')->nullable();
            $table->json('meta')->nullable();
            $table->timestamps();

            $table->index(['session_id', 'created_at']);
        });
    }

    public function down(): void
    {
        Schema::dropIfExists('ext_auto_edit_videos_messages');
    }
};
