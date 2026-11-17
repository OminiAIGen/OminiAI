<?php

declare(strict_types=1);

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    public function up(): void
    {
        Schema::create('ext_video_agent_brand_kits', function (Blueprint $table) {
            $table->id();
            $table->foreignId('user_id')->nullable()->constrained()->nullOnDelete();
            $table->string('heygen_id')->unique();
            $table->string('type')->default('kit');
            $table->string('name')->nullable();
            $table->json('payload')->nullable();
            $table->timestamp('synced_at')->nullable();
            $table->timestamps();

            $table->index(['type', 'user_id']);
        });
    }

    public function down(): void
    {
        Schema::dropIfExists('ext_video_agent_brand_kits');
    }
};
