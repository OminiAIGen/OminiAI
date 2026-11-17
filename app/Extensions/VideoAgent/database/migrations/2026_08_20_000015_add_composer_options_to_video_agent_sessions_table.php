<?php

declare(strict_types=1);

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    /**
     * HeyGen takes neither a duration nor a custom-instructions parameter, so
     * both are folded into the prompt at send time. They are stored here as
     * well so the composer can show what was chosen. The existing `meta`
     * column is not an option: `VideoAgentService::sync()` overwrites it
     * wholesale on the first poll.
     */
    public function up(): void
    {
        Schema::table('ext_video_agent_sessions', function (Blueprint $table): void {
            $table->unsignedInteger('duration_seconds')->nullable()->after('orientation');
            $table->text('instructions')->nullable()->after('duration_seconds');
        });
    }

    public function down(): void
    {
        Schema::table('ext_video_agent_sessions', function (Blueprint $table): void {
            $table->dropColumn(['duration_seconds', 'instructions']);
        });
    }
};
