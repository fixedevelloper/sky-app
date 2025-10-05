<?php

namespace App\Models;

use Illuminate\Contracts\Auth\MustVerifyEmail;
use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\SoftDeletes;
use Illuminate\Foundation\Auth\User as Authenticatable;
use Illuminate\Notifications\Notifiable;
use Laravel\Sanctum\HasApiTokens;

class User extends Authenticatable
{
    use HasApiTokens, HasFactory, Notifiable, SoftDeletes;

    /**
     * Attributs assignables en masse
     */
    protected $fillable = [
        'name',
        'phone',
        'email',
        'image_url',
        'user_type',
        'password',
        'image_cni_recto',
        'image_cni_verso',
        'activity'
    ];

    /**
     * Attributs cachés pour la sérialisation
     */
    protected $hidden = [
        'password',
        'remember_token',
    ];

    /**
     * Attributs castés automatiquement
     */
    protected $casts = [
        'email_verified_at' => 'datetime',
        'user_type' => 'string',
    ];

    // 🔗 Relations

    /**
     * Un utilisateur "vendor" possède plusieurs points de vente
     */
    public function pointSales()
    {
        return $this->hasMany(PointSale::class, 'vendor_id');
    }

    /**
     * Si tu veux récupérer tous les clients liés à ses points de vente
     */
    public function customers()
    {
        return $this->hasManyThrough(Customer::class, PointSale::class, 'vendor_id', 'point_sale_id');
    }

    /**
     * Vérifie si l'utilisateur est Admin
     */
    public function isAdmin(): bool
    {
        return $this->user_type === 'admin';
    }

    /**
     * Vérifie si l'utilisateur est Vendor
     */
    public function isVendor(): bool
    {
        return $this->user_type === 'vendor';
    }

    /**
     * Vérifie si l'utilisateur est Partner
     */
    public function isPartner(): bool
    {
        return $this->user_type === 'partner';
    }
}
