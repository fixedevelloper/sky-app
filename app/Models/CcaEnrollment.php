<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;

class CcaEnrollment extends Model
{
    protected $fillable = [
        'type',
        'name',
        'accounts',
        'niu',
        'position',
        'documents',
    ];

    protected $casts = [
        'documents' => 'array', // convertit JSON en array automatiquement
    ];
}
