<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;

class PasswordChangeLog extends Model
{
    protected $fillable = ['user_id', 'ip_address', 'user_agent'];

    public function user()
    {
        // ✅ explicitly reference User_ID as the owner key
        return $this->belongsTo(User::class, 'user_id', 'User_ID');
    }
}