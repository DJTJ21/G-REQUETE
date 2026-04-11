<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;
use Illuminate\Database\Eloquent\Relations\HasMany;

class Administrateur extends Model
{
    use HasFactory;

    protected $table = 'administrateurs';

    protected $fillable = ['utilisateur_id', 'niveau_acces'];

    public function utilisateur(): BelongsTo
    {
        return $this->belongsTo(User::class, 'utilisateur_id');
    }

    public function historiques(): HasMany
    {
        return $this->hasMany(HistoriqueAction::class, 'admin_id');
    }

    public function getNomCompletAttribute(): string
    {
        return $this->utilisateur->nom_complet;
    }
}
