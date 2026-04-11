<?php

namespace App\Models;

use App\Enums\RoleUtilisateur;
use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Relations\HasMany;
use Illuminate\Database\Eloquent\Relations\HasOne;
use Illuminate\Foundation\Auth\User as Authenticatable;
use Illuminate\Notifications\Notifiable;

class User extends Authenticatable
{
    use HasFactory, Notifiable;

    protected $fillable = [
        'nom',
        'prenom',
        'email',
        'password',
        'role',
        'est_actif',
        'derniere_connexion',
    ];

    protected $hidden = [
        'password',
        'remember_token',
    ];

    protected $casts = [
        'password'          => 'hashed',
        'est_actif'         => 'boolean',
        'derniere_connexion' => 'datetime',
        'role'              => RoleUtilisateur::class,
    ];

    public function getNomCompletAttribute(): string
    {
        return $this->prenom . ' ' . $this->nom;
    }

    public function isEtudiant(): bool
    {
        return $this->role === RoleUtilisateur::Etudiant;
    }

    public function isAgent(): bool
    {
        return $this->role === RoleUtilisateur::Agent;
    }

    public function isAdmin(): bool
    {
        return $this->role === RoleUtilisateur::Admin;
    }

    public function etudiant(): HasOne
    {
        return $this->hasOne(Etudiant::class, 'utilisateur_id');
    }

    public function agent(): HasOne
    {
        return $this->hasOne(AgentScolarite::class, 'utilisateur_id');
    }

    public function administrateur(): HasOne
    {
        return $this->hasOne(Administrateur::class, 'utilisateur_id');
    }

    public function notifications(): HasMany
    {
        return $this->hasMany(NotificationApp::class, 'utilisateur_id');
    }

    public function scopeActif($query)
    {
        return $query->where('est_actif', true);
    }

    public function scopeParRole($query, string $role)
    {
        return $query->where('role', $role);
    }
}
