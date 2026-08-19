<?php

namespace App\Models;

use Illuminate\Foundation\Auth\User as Authenticatable; // 1. Ubah Model menjadi Authenticatable
use Illuminate\Database\Eloquent\Relations\BelongsTo;
use Laravel\Sanctum\HasApiTokens; // 2. Import trait Sanctum

class ApiServiceClient extends Authenticatable
{
    use HasApiTokens; // 3. Pasang trait Sanctum di sini

    protected $table = 'api_service_clients';

    protected $fillable = [
        'name',
        'client_id',
        'secret_hash',
        'allowed_purposes',
        'allowed_abilities',
        'allowed_ips',
        'expires_at',
        'is_active',
        'created_by',
    ];

    protected $hidden = [
        'secret_hash',
    ];

    protected function casts(): array
    {
        return [
            'allowed_purposes' => 'array',
            'allowed_abilities' => 'array',
            'allowed_ips' => 'array',
            'expires_at' => 'datetime',
            'is_active' => 'boolean',
        ];
    }

    public function creator(): BelongsTo
    {
        return $this->belongsTo(User::class, 'created_by');
    }

    public function isExpired(): bool
    {
        return $this->expires_at !== null
            && $this->expires_at->isPast();
    }

    public function isUsable(): bool
    {
        return $this->is_active && ! $this->isExpired();
    }

    public function allowsPurpose(string $purpose): bool
    {
        return in_array(
            $purpose,
            $this->allowed_purposes ?? [],
            true
        );
    }

    public function allowsAbility(string $ability): bool
    {
        return in_array(
            $ability,
            $this->allowed_abilities ?? [],
            true
        );
    }
}