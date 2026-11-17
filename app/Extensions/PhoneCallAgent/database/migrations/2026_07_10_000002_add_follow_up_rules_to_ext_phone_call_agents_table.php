<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    public function up(): void
    {
        Schema::table('ext_phone_call_agents', function (Blueprint $table) {
            $table->json('follow_up_rules')->nullable()->after('booking_tool_ids');
        });
    }

    public function down(): void
    {
        Schema::table('ext_phone_call_agents', function (Blueprint $table) {
            $table->dropColumn('follow_up_rules');
        });
    }
};
