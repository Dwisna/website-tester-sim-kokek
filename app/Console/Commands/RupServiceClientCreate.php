<?php

namespace App\Console\Commands;

use Illuminate\Console\Command;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Hash;
use Illuminate\Support\Str;
use Carbon\Carbon;

class RupServiceClientCreate extends Command
{
    // 1. Tambahkan parameter dan opsi pada signature
    protected $signature = 'rup:service-client:create
                            {email : Email PIC atau pemilik client}
                            {--name= : Nama service client}
                            {--days=365 : Masa berlaku token dalam hari}
                            {--ip=* : IP Allowlist (bisa dipanggil berulang, misal --ip=127.0.0.1)}
                            {--purpose=gateway : Purpose dari client ini}';

    protected $description = 'Membuat service client untuk akses API RUP beserta ID dan Secret';

    public function handle(): int
    {
        // Menangkap input dari terminal
        $email = $this->argument('email');
        $name = $this->option('name') ?: 'API Client - ' . $email;
        $days = (int) $this->option('days');
        $ips = $this->option('ip');
        $purpose = $this->option('purpose');

        // Generate Client ID dan Secret acak
        $clientId = 'rup_client_' . Str::random(15);
        $plainSecret = 'secret_' . Str::random(32);

        // 2. Simpan ke database (sesuaikan nama tabel dengan migration Anda, misal: api_service_clients)
        DB::table('api_service_clients')->insert([
            'name' => $name,
            'client_id' => $clientId,
            // Secret di-hash agar aman di database
            'secret_hash' => Hash::make($plainSecret),
            'allowed_purposes' => json_encode([$purpose]),
            'allowed_abilities' => json_encode(['application:access', 'posts:read']),
            'allowed_ips' => empty($ips) ? null : json_encode($ips),
            'expires_at' => Carbon::now()->addDays($days),
            'is_active' => true,
            'created_at' => now(),
            'updated_at' => now(),
        ]);

        // 3. Cetak hasil ke terminal untuk disalin ke .env project Client
        $this->newLine();
        $this->info(' RUP Service Client berhasil dibuat!');
        $this->line('--------------------------------------------------');
        $this->line('Email       : ' . $email);
        $this->line('Name        : ' . $name);
        $this->line('Client ID   : <fg=green>' . $clientId . '</>');
        $this->line('Secret      : <fg=red>' . $plainSecret . '</>');
        $this->line('Expires At  : ' . Carbon::now()->addDays($days)->format('Y-m-d H:i:s'));
        
        if (!empty($ips)) {
            $this->line('Allowed IPs : ' . implode(', ', $ips));
        }
        
        $this->line('--------------------------------------------------');
        $this->warn('⚠️ COPY DAN SIMPAN SECRET DI ATAS SEKARANG.');
        $this->warn('Secret ini hanya ditampilkan satu kali dan disimpan dalam bentuk hash di database.');
        $this->newLine();

        return self::SUCCESS;
    }
}