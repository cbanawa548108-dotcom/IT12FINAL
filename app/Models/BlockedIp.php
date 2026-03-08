<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;

class BlockedIp extends Model
{
    protected $fillable = [
        'ip_address',
        'user_id',
        'reason',
        'blocked_until',
        'blocked_by',
    ];

    protected $casts = [
        'blocked_until' => 'datetime',
    ];

    public function isExpired(): bool
    {
        return $this->blocked_until && $this->blocked_until->isPast();
    }

    public function blocker()
    {
        return $this->belongsTo(User::class, 'blocked_by', 'User_ID');
    }

    public function blockedUser()
    {
        return $this->belongsTo(User::class, 'user_id', 'User_ID');
    }
}