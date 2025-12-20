<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;

class Pmes extends Model
{
    const STATUS_PENDING = 'pending';
    const STATUS_PAID = 'paid';
    const STATUS_FAILED = 'failed';
    const STATUS_CANCELLED = 'cancelled';
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

        'status',
    ];
}
