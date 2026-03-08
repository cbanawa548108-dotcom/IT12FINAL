<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class LoginAuditLog extends Model
{
    use HasFactory;

    protected $fillable = [
        'email',
        'user_id',
        'status',
        'ip_address',
        'user_agent',
        'failure_reason',
        'attempts_count',
        'locked_until',
    ];

    protected $casts = [
        'locked_until' => 'datetime',
    ];

    public function user()
    {
        return $this->belongsTo(User::class);
    }
}