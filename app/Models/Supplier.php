<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\SoftDeletes;

class Supplier extends Model
{
    use SoftDeletes;

    protected $primaryKey = 'Supplier_ID';

    protected $fillable = [
        'Supplier_Name', 'contact_person', 'contact_number', 'address', 'payment_terms'
    ];

    protected $dates = ['deleted_at'];

    // Phone masking accessor
    public function getMaskedContactNumberAttribute()
    {
        $number = $this->contact_number;
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

    public function products()
    {
        return $this->hasMany(Product::class, 'Supplier_ID');
    }

    public function transactions()
    {
        return $this->hasMany(SupplierTransaction::class, 'Supplier_ID');
    }
}