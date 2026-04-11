<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\HasMany;

class Departement extends Model
{
    use HasFactory;

    protected $fillable = ['nom_dep', 'description'];

    public function filieres(): HasMany
    {
        return $this->hasMany(Filiere::class);
    }
}
