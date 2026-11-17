<?php

declare(strict_types=1);

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    public function up(): void
    {
        Schema::create('ext_marketing_campaign_copilot_messages', function (Blueprint $table): void {
            $table->id();
            $table->foreignId('campaign_id')->nullable()->index()->constrained('ext_marketing_campaigns')->cascadeOnDelete();
            $table->string('role')->default('user');
            $table->longText('content')->nullable();
            $table->json('tool_calls')->nullable();
            $table->json('metadata')->nullable();
            $table->timestamps();
        });
    }

    public function down(): void
    {
        Schema::dropIfExists('ext_marketing_campaign_copilot_messages');
    }
};
