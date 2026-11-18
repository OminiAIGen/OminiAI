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
        Schema::table('exported_videos', function (Blueprint $table) {
            if (! Schema::hasColumn('exported_videos', 'credits_used')) {
                $table->decimal('credits_used', 12, 2)->nullable();
            }
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::table('exported_videos', function (Blueprint $table) {
            if (Schema::hasColumn('exported_videos', 'credits_used')) {
                $table->dropColumn('credits_used');
            }
        });
    }
};
