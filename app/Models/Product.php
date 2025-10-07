<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\SoftDeletes;

class Product extends Model
{
    use HasFactory, SoftDeletes;

    protected $fillable = [
        'name',
        'memory',
        'price',
        'price_leasing',
        'image_url',
        'category_id'
    ];

    // 🔗 Relations
    public function category()
    {
        return $this->belongsTo(Category::class);
    }

}
