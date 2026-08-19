<?php

namespace App\Console\Commands;

use App\Models\User;
use Illuminate\Console\Command;

class PromoteAdmin extends Command
{
    /**
     * Nama dan signature perintah artisan.
     */
    protected $signature = 'rup:promote-admin {email : Email pengguna yang akan dijadikan admin}';

    /**
     * Deskripsi perintah.
     */
    protected $description = 'Mempromosikan pengguna menjadi admin';

    /**
     * Eksekusi perintah.
     */
    public function handle()
    {
        $email = $this->argument('email');

        // 1. Cari user berdasarkan email
        $user = User::where('email', $email)->first();

        if (!$user) {
            $this->error("Pengguna dengan email '{$email}' tidak ditemukan.");
            return self::FAILURE;
        }

        // 2. Set role / status admin (sesuaikan dengan kolom di tabel users Anda)
        if (\Schema::hasColumn('users', 'role')) {
            $user->role = 'admin';
        }
        
        if (\Schema::hasColumn('users', 'is_admin')) {
            $user->is_admin = true;
        }

        $user->save();

        $this->info("Pengguna {$user->name} ({$email}) berhasil dipromosikan menjadi Admin.");
        return self::SUCCESS;
    }
}