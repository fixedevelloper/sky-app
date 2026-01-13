<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\SoftDeletes;

class Pme extends Model
{
    use SoftDeletes;

    protected $fillable = [
        'referenceId',
        'operator',
        'name_entreprise',
        'name_responsable',
        'poste_responsable',
        'amount_bc',
        'number_souscripteur',
        'number_echeance_paiement',
        'montant_total',
        'name_gestionnaire',
        'name_manager',
        'image_bc',
        'image_bl',
        'image_facture',
        'image_avi',
        'image_pl',
        'image_contract1',
        'image_contract2',
        'status',
        'user_id',
        'confirmed_at',
    ];

    protected $casts = [
        'referenceId' => 'string',
        'confirmed_at' => 'datetime',
        'amount_bc' => 'float',
        'montant_total' => 'float',
    ];

    public function user()
    {
        return $this->belongsTo(User::class);
    }
}

