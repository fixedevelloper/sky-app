<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\SoftDeletes;

class Purchase extends Model
{
    use HasFactory, SoftDeletes;

    protected $fillable = [
        'product_name',
        'price',
        'amount_by_day',
        'payment_mode',
        'image_url',
        'customer_id'
    ];

    // 🔗 Relations
    public function customer()
    {
        return $this->belongsTo(Customer::class);
    }

    public function paiements()
    {
        return $this->hasMany(Paiement::class);
    }
}
