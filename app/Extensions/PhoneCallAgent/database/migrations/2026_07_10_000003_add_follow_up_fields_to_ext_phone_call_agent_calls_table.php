<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    public function up(): void
    {
        Schema::table('ext_phone_call_agent_calls', function (Blueprint $table) {
            $table->string('source')->default('manual')->index()->after('direction');
            $table->foreignId('booking_id')->nullable()->after('campaign_id')
                ->constrained('ext_phone_call_agent_bookings')->nullOnDelete();
            $table->text('follow_up_instruction')->nullable()->after('status_reason');
        });
    }

    public function down(): void
    {
        Schema::table('ext_phone_call_agent_calls', function (Blueprint $table) {
            $table->dropConstrainedForeignId('booking_id');
            $table->dropColumn(['source', 'follow_up_instruction']);
        });
    }
};
