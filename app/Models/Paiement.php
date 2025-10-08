<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\SoftDeletes;

class Paiement extends Model
{
    use HasFactory, SoftDeletes;

    protected $fillable = [
        'phone',
        'amount',
        'amount_rest',
        'operator',
        'status',
        'purchase_id',
        'reference_id',
        'confirmed_at'
    ];

    // 🔗 Relations
    public function purchase()
    {
        return $this->belongsTo(Purchase::class);
    }
}
