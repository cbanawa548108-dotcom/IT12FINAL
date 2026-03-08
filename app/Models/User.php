<?php

namespace App\Models;

use Illuminate\Foundation\Auth\User as Authenticatable;
use Illuminate\Database\Eloquent\SoftDeletes;

class User extends Authenticatable
{
    use SoftDeletes;

    protected $primaryKey = 'User_ID';

    protected $fillable = [
        'fname', 'lname', 'contact_number', 'role', 'email', 'password',
        'two_factor_code', 'two_factor_expires_at',
    ];

    protected $hidden = ['password'];

    protected $dates = ['deleted_at'];

    protected function casts(): array
    {
        return [
            'password'              => 'hashed',
            'two_factor_expires_at' => 'datetime',
        ];
    }

    // ── Masked Accessors ─────────────────────────────────────

    public function getMaskedEmailAttribute(): string
    {
        $parts  = explode('@', $this->email);
        $name   = $parts[0] ?? '';
        $domain = $parts[1] ?? '';
        return substr($name, 0, 2) . str_repeat('*', max(strlen($name) - 2, 3)) . '@' . $domain;
    }

    public function getMaskedContactNumberAttribute(): ?string
    {
        $phone = $this->contact_number;
        if (!$phone) return null;
        return substr($phone, 0, 3) . str_repeat('*', max(strlen($phone) - 6, 3)) . substr($phone, -3);
    }

    // ── 2FA helpers ──────────────────────────────────────────

    public function generateTwoFactorCode(): string
    {
        $code = str_pad(random_int(0, 999999), 6, '0', STR_PAD_LEFT);

        $this->update([
            'two_factor_code'       => $code,
            'two_factor_expires_at' => now()->addMinutes(10),
        ]);

        return $code;
    }

    public function clearTwoFactorCode(): void
    {
        $this->update([
            'two_factor_code'       => null,
            'two_factor_expires_at' => null,
        ]);
    }

    public function isValidTwoFactorCode(string $code): bool
    {
        return $this->two_factor_code === $code
            && $this->two_factor_expires_at
            && now()->lessThanOrEqualTo($this->two_factor_expires_at);
    }

    // ── Role helpers ─────────────────────────────────────────

    public function isAdmin(): bool   { return $this->role === 'admin'; }
    public function isManager(): bool { return $this->role === 'manager'; }
    public function isCashier(): bool { return $this->role === 'cashier'; }

    public function hasRole($role): bool           { return $this->role === $role; }
    public function hasAnyRole(array $roles): bool { return in_array($this->role, $roles); }

    public function getFullNameAttribute(): string
    {
        return "{$this->fname} {$this->lname}";
    }
}