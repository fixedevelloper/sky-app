<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class MomoCallback extends Model
{
    use HasFactory;

    protected $fillable = [
        'reference_id',
        'status',
        'amount',
        'payload',
    ];

    protected $casts = [
        'payload' => 'array',
    ];
}
