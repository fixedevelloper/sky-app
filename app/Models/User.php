<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Foundation\Auth\User as Authenticatable;
use Illuminate\Notifications\Notifiable;
use Illuminate\Database\Eloquent\SoftDeletes;
use Laravel\Sanctum\HasApiTokens;

class User extends Authenticatable
{
    use HasApiTokens, HasFactory, Notifiable, SoftDeletes;

    protected $fillable = [
        'name', 'phone', 'email', 'password', 'image_url',
        'image_cni_recto', 'image_cni_verso',
        'activity', 'localisation', 'user_type', 'email_verified_at','roles'
    ];

    /**
     * Attributs cachés pour la sérialisation
     */
    protected $hidden = [
        'password', 'remember_token',
    ];

    protected $casts = [
        'email_verified_at' => 'datetime',
        'roles' => 'array', // 🔥 TRÈS IMPORTANT
    ];

    // Relations
    public function pointSales()
    {
        return $this->hasMany(PointSale::class, 'vendor_id');
    }

    public function partners()
    {
        return $this->hasMany(Partner::class);
    }

    public function customers()
    {
        return $this->hasMany(Customer::class);
    }

    public function orders()
    {
        return $this->hasMany(Order::class);
    }

    public function pmes()
    {
        return $this->hasMany(Pme::class);
    }
    /* ======================
      Helpers pour les rôles
      ====================== */

    public function hasRole(string $role): bool
    {
        return in_array($role, $this->roles ?? []);
    }

    public function hasAnyRole(array $roles): bool
    {
        return !empty(array_intersect($roles, $this->roles ?? []));
    }

    public function addRole(string $role): void
    {
        $roles = $this->roles ?? [];
        if (!in_array($role, $roles)) {
            $roles[] = $role;
            $this->roles = $roles;
            $this->save();
        }
    }

    public function removeRole(string $role): void
    {
        $this->roles = array_values(
            array_diff($this->roles ?? [], [$role])
        );
        $this->save();
    }
}

