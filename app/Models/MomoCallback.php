<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Factories\HasFactory;

class MomoCallback extends Model
{
    use HasFactory;

    protected $fillable = ['reference_id','status','amount','payload'];

    protected $casts = [
        'payload' => 'array',
    ];
}
