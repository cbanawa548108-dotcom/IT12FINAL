<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\SoftDeletes;

class Customer extends Model
{
    use SoftDeletes;

    protected $primaryKey = 'Customer_ID';
    
    public $incrementing = true;

    protected $fillable = [
        'Customer_Name',
        'Contact_Number',
        'address',
        'email',
        'credit_limit',
        'payment_terms'
    ];

    protected $dates = ['deleted_at'];

    protected $casts = [
        'deleted_at' => 'datetime',
    ];

    // Phone masking accessor
    public function getMaskedContactNumberAttribute()
    {
        $number = $this->Contact_Number;
        if (!$number) return 'N/A';

        // Remove any dashes/spaces to normalize
        $clean = preg_replace('/[\s\-]/', '', $number);

        // Mask middle digits: 0917-XXX-XXXX
        // Keep first 4, mask next 3, keep last 4
        if (strlen($clean) >= 11) {
            return substr($clean, 0, 4) . '-XXX-' . substr($clean, -4);
        }

        return $number; // return as-is if format is unexpected
    }

    public function salesTransactions()
    {
        return $this->hasMany(SalesTransaction::class, 'Customer_ID', 'Customer_ID');
    }

    public function sales()
    {
        return $this->hasMany(SalesTransaction::class, 'Customer_ID', 'Customer_ID');
    }
}