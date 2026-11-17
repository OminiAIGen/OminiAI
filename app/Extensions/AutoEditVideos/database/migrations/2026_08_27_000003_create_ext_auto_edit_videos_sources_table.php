<?php

declare(strict_types=1);

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    public function up(): void
    {
        Schema::create('ext_auto_edit_videos_sources', function (Blueprint $table) {
            $table->id();
            $table->foreignId('session_id')->nullable()->constrained('ext_auto_edit_videos_sessions')->cascadeOnDelete();
            $table->foreignId('user_id')->constrained()->cascadeOnDelete();
            $table->string('type')->default('upload');
            $table->string('original_name')->nullable();
            $table->string('local_path')->nullable();
            $table->text('public_url')->nullable();
            $table->string('mime_type')->nullable();
            $table->unsignedBigInteger('size_bytes')->nullable();
            $table->unsignedInteger('duration_seconds')->nullable();
            $table->unsignedInteger('width')->nullable();
            $table->unsignedInteger('height')->nullable();
            $table->timestamps();

            $table->index(['user_id', 'session_id']);
        });
    }

    public function down(): void
    {
        Schema::dropIfExists('ext_auto_edit_videos_sources');
    }
};
