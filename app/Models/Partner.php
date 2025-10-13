<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;

class Partner extends Model
{
    protected $fillable=[
      'categories','user_id'
    ];
    // ✅ Laravel convertira automatiquement JSON <-> array
    protected $casts = [
        'categories' => 'array',
    ];
}
