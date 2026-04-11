<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;
use Illuminate\Database\Eloquent\Relations\BelongsToMany;
use Illuminate\Database\Eloquent\Relations\HasMany;

class PvRequete extends Model
{
    use HasFactory;

    protected $table = 'pv_requetes';

    protected $fillable = ['agent_id', 'session_id', 'ref_pv', 'date_generation', 'chemin_pdf'];

    protected $casts = [
        'date_generation' => 'datetime',
    ];

    public function agent(): BelongsTo
    {
        return $this->belongsTo(AgentScolarite::class, 'agent_id');
    }

    public function session(): BelongsTo
    {
        return $this->belongsTo(SessionExamen::class, 'session_id');
    }

    public function lignes(): HasMany
    {
        return $this->hasMany(PvRequeteLigne::class, 'pv_id')->orderBy('ordre_affichage');
    }

    public function requetes(): BelongsToMany
    {
        return $this->belongsToMany(Requete::class, 'pv_requete_lignes', 'pv_id', 'requete_id')
            ->withPivot('ordre_affichage')
            ->withTimestamps();
    }
}
