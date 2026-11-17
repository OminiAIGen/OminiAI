<?php

use App\Models\User;
use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    public function up(): void
    {
        Schema::create('ext_phone_call_agent_campaigns', function (Blueprint $table) {
            $table->id();
            $table->uuid('uuid')->unique();
            $table->foreignId('agent_id')->constrained('ext_phone_call_agents')->cascadeOnDelete();
            $table->foreignIdFor(User::class)->constrained()->cascadeOnDelete();
            $table->string('name');
            $table->string('status')->default('pending');
            $table->timestamp('scheduled_at')->nullable();
            $table->integer('total_recipients')->default(0);
            $table->integer('completed_count')->default(0);
            $table->integer('failed_count')->default(0);
            $table->timestamps();
        });
    }

    public function down(): void
    {
        Schema::dropIfExists('ext_phone_call_agent_campaigns');
    }
};
