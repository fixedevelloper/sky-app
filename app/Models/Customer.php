<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\SoftDeletes;

class Customer extends Model
{
    use HasFactory, SoftDeletes;

    protected $fillable = [
        'image_cni_recto','image_cni_verso','image_url',
        'user_id','point_sale_id'
    ];

    public function user()
    {
        return $this->belongsTo(User::class);
    }

    public function pointSale()
    {
        return $this->belongsTo(PointSale::class);
    }
}
