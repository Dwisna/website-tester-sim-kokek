<?php

namespace App\Console\Commands;

use App\Models\User;
use Illuminate\Console\Command;
use Illuminate\Support\Carbon;

class IssueApiToken extends Command
{
    protected $signature = 'api:token
        {email : Email user yang menerima token}
        {--days=30 : Masa berlaku token dalam hari}
        {--name=n8n : Nama token}
        {--ability=n8n:import : Ability token}
        {--revoke-existing : Cabut token dengan nama yang sama sebelum membuat token baru}';

    protected $description = 'Menerbitkan Laravel Sanctum Personal Access Token dengan ability dan expiry';

    public function handle(): int
    {
        $user = User::where('email', $this->argument('email'))->first();

        if (!$user) {
            $this->error('User dengan email tersebut tidak ditemukan.');
            return self::FAILURE;
        }

        $days = (int) $this->option('days');
        if ($days < 1 || $days > 3650) {
            $this->error('--days harus antara 1 sampai 3650 hari.');
            return self::FAILURE;
        }

        $name = trim((string) $this->option('name'));
        $ability = trim((string) $this->option('ability'));

        if ($name === '' || $ability === '') {
            $this->error('Nama token dan ability wajib diisi.');
            return self::FAILURE;
        }

        if ($this->option('revoke-existing')) {
            $user->tokens()->where('name', $name)->delete();
        }

        $expiresAt = Carbon::now()->addDays($days);

        $token = $user->createToken($name, [$ability], $expiresAt);

        $this->newLine();
        $this->info('Sanctum token berhasil dibuat.');
        $this->line('User    : ' . $user->email);
        $this->line('Name    : ' . $name);
        $this->line('Ability : ' . $ability);
        $this->line('Expired : ' . $expiresAt->format('Y-m-d H:i:s'));
        $this->newLine();
        $this->warn('TOKEN HANYA DITAMPILKAN SEKALI. Simpan di credential/secret manager n8n atau .env.');
        $this->line($token->plainTextToken);
        $this->newLine();

        return self::SUCCESS;
    }
}
