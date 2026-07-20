<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    public function up(): void
    {
        Schema::create('n8n_webhook_logs', function (Blueprint $table) {
            $table->id();
            $table->string('source')->nullable();
            $table->string('event')->nullable();
            $table->string('channel')->nullable();
            $table->json('payload')->nullable();
            $table->text('message')->nullable();
            $table->string('customer')->nullable();
            $table->string('status')->default('accepted');
            $table->timestamps();
        });
    }

    public function down(): void
    {
        Schema::dropIfExists('n8n_webhook_logs');
    }
};
