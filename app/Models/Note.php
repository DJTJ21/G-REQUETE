<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;

class Note extends Model
{
    use HasFactory;

    protected $table = 'notes';

    protected $fillable = [
        'requete_id', 'etudiant_id', 'cours_id',
        'note_avant', 'note_apres', 'modifie_par', 'date_modification',
    ];

    protected $casts = [
        'note_avant'       => 'decimal:2',
        'note_apres'       => 'decimal:2',
        'date_modification' => 'datetime',
    ];

    public function requete(): BelongsTo
    {
        return $this->belongsTo(Requete::class);
    }

    public function etudiant(): BelongsTo
    {
        return $this->belongsTo(Etudiant::class);
    }

    public function cours(): BelongsTo
    {
        return $this->belongsTo(Cours::class);
    }

    public function agent(): BelongsTo
    {
        return $this->belongsTo(AgentScolarite::class, 'modifie_par');
    }
}
