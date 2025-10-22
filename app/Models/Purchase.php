<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\SoftDeletes;

class Purchase extends Model
{
    use HasFactory, SoftDeletes;

    protected $fillable = [
        'product_id',
        'pay_type',
        'payment_mode',
        'image_url',
        'customer_id',
        'is_custom_product'
    ];

    // 🔗 Relations
    public function product()
    {
        return $this->belongsTo(Product::class);
    }

    public function customer()
    {
        return $this->belongsTo(Customer::class);
    }

    public function paiements()
    {
        return $this->hasMany(Paiement::class);
    }
   public function customProduct()
    {
        return $this->hasOne(CustomProduct::class);
    }

}
