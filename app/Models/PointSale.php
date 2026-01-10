<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\SoftDeletes;

class PointSale extends Model
{
    use HasFactory, SoftDeletes;

    protected $fillable = [
        'name','activity','localisation','phone',
        'image_url','image_doc_fiscal','operator','referenceId','status','vendor_id'
    ];

    public function vendor()
    {
        return $this->belongsTo(User::class, 'vendor_id');
    }

    public function customers()
    {
        return $this->hasMany(Customer::class);
    }
}
