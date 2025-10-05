<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\SoftDeletes;

class Customer extends Model
{
    use HasFactory, SoftDeletes;

    protected $fillable = [
        'name',
        'phone',
        'activity',
        'localisation',
        'commercial_code',
        'code_key_account',
        'image_cni_recto',
        'image_cni_verso',
        'image_url',
        'point_sale_id'
    ];

    // 🔗 Relations
    public function pointSale()
    {
        return $this->belongsTo(PointSale::class);
    }

    public function purchases()
    {
        return $this->hasMany(Purchase::class);
    }
}
