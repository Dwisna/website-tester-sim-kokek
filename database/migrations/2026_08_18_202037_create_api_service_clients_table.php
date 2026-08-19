<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    public function up(): void
    {
        Schema::create('api_service_clients', function (Blueprint $table) {
            $table->id();

            // Identitas aplikasi
            $table->string('name');
            $table->string('client_id', 100)->unique();

            // Secret disimpan dalam bentuk hash
            $table->string('secret_hash');

            // Purpose yang diperbolehkan
            $table->json('allowed_purposes')->nullable();

            // Ability yang diperbolehkan
            $table->json('allowed_abilities')->nullable();

            // IP server client yang diperbolehkan
            $table->json('allowed_ips')->nullable();

            // Masa berlaku service client
            $table->timestamp('expires_at')->nullable();

            // Bisa dinonaktifkan tanpa menghapus data
            $table->boolean('is_active')->default(true);

            // Siapa user/admin yang membuatnya
            $table->foreignId('created_by')
                ->nullable()
                ->constrained('users')
                ->nullOnDelete();

            $table->timestamps();

            $table->index('is_active');
            $table->index('expires_at');
        });
    }

    public function down(): void
    {
        Schema::dropIfExists('api_service_clients');
    }
};