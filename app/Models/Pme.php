<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\SoftDeletes;

class Pme extends Model
{
    use HasFactory, SoftDeletes;

    protected $fillable = [
        'referenceId','operator','name_entreprise','name_responsable',
        'poste_responsable','amount_bc','number_souscripteur',
        'number_echeance_paiement','montant_total','name_gestionnaire',
        'name_manager','image_bc','image_bl','image_facture',
        'status','user_id','confirmed_at'
    ];

    protected $casts = [
        'confirmed_at' => 'datetime',
    ];

    public function user()
    {
        return $this->belongsTo(User::class);
    }
}
