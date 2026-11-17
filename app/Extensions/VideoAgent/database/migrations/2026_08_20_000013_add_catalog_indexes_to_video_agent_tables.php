<?php

declare(strict_types=1);

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

/**
 * The catalog tables hold thousands of rows and every listing sorts by `name`
 * while filtering on a single column, which the existing composite indexes
 * cannot serve because `name`/`language`/`gender` are not leading columns.
 */
return new class extends Migration
{
    public function up(): void
    {
        Schema::table('ext_video_agent_voices', function (Blueprint $table) {
            $table->index('name', 'ext_video_agent_voices_name_index');
            $table->index('language', 'ext_video_agent_voices_language_index');
            $table->index('gender', 'ext_video_agent_voices_gender_index');
        });

        Schema::table('ext_video_agent_avatars', function (Blueprint $table) {
            $table->index('name', 'ext_video_agent_avatars_name_index');
        });

        Schema::table('ext_video_agent_styles', function (Blueprint $table) {
            $table->index('name', 'ext_video_agent_styles_name_index');
        });
    }

    public function down(): void
    {
        Schema::table('ext_video_agent_voices', function (Blueprint $table) {
            $table->dropIndex('ext_video_agent_voices_name_index');
            $table->dropIndex('ext_video_agent_voices_language_index');
            $table->dropIndex('ext_video_agent_voices_gender_index');
        });

        Schema::table('ext_video_agent_avatars', function (Blueprint $table) {
            $table->dropIndex('ext_video_agent_avatars_name_index');
        });

        Schema::table('ext_video_agent_styles', function (Blueprint $table) {
            $table->dropIndex('ext_video_agent_styles_name_index');
        });
    }
};
