<?php

namespace App\Models;

use App\Enums\CycleFiliere;
use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;
use Illuminate\Database\Eloquent\Relations\HasMany;

class Filiere extends Model
{
    use HasFactory;

    protected $fillable = ['departement_id', 'nom_filiere', 'cycle'];

    protected $casts = [
        'cycle' => CycleFiliere::class,
    ];

    public function departement(): BelongsTo
    {
        return $this->belongsTo(Departement::class);
    }

    public function cours(): HasMany
    {
        return $this->hasMany(Cours::class);
    }

    public function etudiants(): HasMany
    {
        return $this->hasMany(Etudiant::class);
    }
}
