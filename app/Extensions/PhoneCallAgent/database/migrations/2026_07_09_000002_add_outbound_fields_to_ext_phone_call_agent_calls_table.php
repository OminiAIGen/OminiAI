<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    public function up(): void
    {
        Schema::table('ext_phone_call_agent_calls', function (Blueprint $table) {
            $table->string('direction')->default('inbound')->index()->after('called_number');
            $table->foreignId('campaign_id')->nullable()->after('agent_id')
                ->constrained('ext_phone_call_agent_campaigns')->nullOnDelete();
            $table->timestamp('scheduled_at')->nullable()->index()->after('started_at');
        });
    }

    public function down(): void
    {
        Schema::table('ext_phone_call_agent_calls', function (Blueprint $table) {
            $table->dropConstrainedForeignId('campaign_id');
            $table->dropColumn(['direction', 'scheduled_at']);
        });
    }
};
