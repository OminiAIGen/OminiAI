<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    /**
     * Run the migrations.
     */
    public function up(): void
    {
        Schema::table('ai_music_pro', function (Blueprint $table) {
            if (! Schema::hasColumn('ai_music_pro', 'credits_used')) {
                $table->decimal('credits_used', 12, 2)->nullable()->after('lyrics');
            }
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::table('ai_music_pro', function (Blueprint $table) {
            if (Schema::hasColumn('ai_music_pro', 'credits_used')) {
                $table->dropColumn('credits_used');
            }
        });
    }
};
