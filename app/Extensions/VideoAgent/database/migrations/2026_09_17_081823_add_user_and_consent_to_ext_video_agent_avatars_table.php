<?php

declare(strict_types=1);

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    public function up(): void
    {
        Schema::table('ext_video_agent_avatars', function (Blueprint $table) {
            $table->foreignId('user_id')->nullable()->after('id')->constrained('users')->nullOnDelete();
            $table->string('consent_status')->nullable()->after('status');
            $table->timestamp('last_polled_at')->nullable()->after('synced_at');
            $table->index(['user_id', 'avatar_type']);
        });
    }

    public function down(): void
    {
        Schema::table('ext_video_agent_avatars', function (Blueprint $table) {
            $table->dropForeign(['user_id']);
            $table->dropIndex(['user_id', 'avatar_type']);
            $table->dropColumn(['user_id', 'consent_status', 'last_polled_at']);
        });
    }
};
