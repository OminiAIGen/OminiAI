<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    public function up(): void
    {
        Schema::table('plans', function (Blueprint $table) {
            if (! Schema::hasColumn('plans', 'phone_call_agent_outbound_seconds_limit')) {
                $table->integer('phone_call_agent_outbound_seconds_limit')->default(-1);
            }

            if (! Schema::hasColumn('plans', 'phone_call_agent_outbound_calls_per_day')) {
                $table->integer('phone_call_agent_outbound_calls_per_day')->default(-1);
            }
        });
    }

    public function down(): void
    {
        Schema::table('plans', function (Blueprint $table) {
            $table->dropColumn([
                'phone_call_agent_outbound_seconds_limit',
                'phone_call_agent_outbound_calls_per_day',
            ]);
        });
    }
};
