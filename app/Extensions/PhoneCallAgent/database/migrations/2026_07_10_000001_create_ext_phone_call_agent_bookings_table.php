<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    public function up(): void
    {
        Schema::create('ext_phone_call_agent_bookings', function (Blueprint $table) {
            $table->id();
            $table->uuid('uuid')->unique();
            $table->foreignId('agent_id')->constrained('ext_phone_call_agents')->cascadeOnDelete();
            $table->foreignId('user_id')->constrained('users')->cascadeOnDelete();
            $table->string('provider')->nullable();
            $table->string('provider_uid')->index();
            $table->string('customer_name')->nullable();
            $table->string('customer_email')->nullable();
            $table->string('customer_phone')->nullable();
            $table->timestamp('starts_at')->nullable();
            $table->timestamp('ends_at')->nullable();
            $table->string('timezone')->nullable();
            $table->string('status')->default('confirmed');
            $table->timestamps();
        });
    }

    public function down(): void
    {
        Schema::dropIfExists('ext_phone_call_agent_bookings');
    }
};
