<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;

class CustomProduct extends Model
{
    protected $fillable=[
        'name','status','amount','exact_amount','purchase_id'
    ];
    public function purchase()
    {
        return $this->belongsTo(Purchase::class);
    }

}
