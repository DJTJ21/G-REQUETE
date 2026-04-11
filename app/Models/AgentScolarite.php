<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;
use Illuminate\Database\Eloquent\Relations\HasMany;

class AgentScolarite extends Model
{
    use HasFactory;

    protected $table = 'agents_scolarite';

    protected $fillable = ['utilisateur_id', 'matricule_agent', 'service'];

    public function utilisateur(): BelongsTo
    {
        return $this->belongsTo(User::class, 'utilisateur_id');
    }

    public function requetes(): HasMany
    {
        return $this->hasMany(Requete::class, 'agent_id');
    }

    public function pvRequetes(): HasMany
    {
        return $this->hasMany(PvRequete::class, 'agent_id');
    }

    public function historiques(): HasMany
    {
        return $this->hasMany(HistoriqueAction::class, 'agent_id');
    }

    public function getNomCompletAttribute(): string
    {
        return $this->utilisateur->nom_complet;
    }
}
