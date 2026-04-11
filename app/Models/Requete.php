<?php

namespace App\Models;

use App\Enums\StatutRequete;
use App\Enums\TypeAnomalie;
use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;
use Illuminate\Database\Eloquent\Relations\HasMany;
use Illuminate\Database\Eloquent\Relations\HasOne;

class Requete extends Model
{
    use HasFactory;

    protected $table = 'requetes';

    protected $fillable = [
        'ref_requete', 'etudiant_id', 'cours_id', 'session_id', 'agent_id',
        'type_anomalie', 'description', 'statut', 'motif_rejet',
        'date_soumission', 'date_prise_en_charge', 'date_traitement',
    ];

    protected $casts = [
        'statut'              => StatutRequete::class,
        'type_anomalie'       => TypeAnomalie::class,
        'date_soumission'     => 'datetime',
        'date_prise_en_charge' => 'datetime',
        'date_traitement'     => 'datetime',
    ];

    protected static function booted(): void
    {
        static::updated(function (Requete $requete) {
            if ($requete->wasChanged('statut')) {
                $message = match ($requete->statut) {
                    StatutRequete::EnCoursVerification => 'Votre requête ' . $requete->ref_requete . ' est en cours de vérification.',
                    StatutRequete::TraiteeFondee        => 'Votre requête ' . $requete->ref_requete . ' a été traitée et déclarée fondée.',
                    StatutRequete::TraiteeNonFondee     => 'Votre requête ' . $requete->ref_requete . ' a été traitée et déclarée non fondée.',
                    default                             => 'Le statut de votre requête ' . $requete->ref_requete . ' a changé.',
                };

                NotificationApp::create([
                    'utilisateur_id' => $requete->etudiant->utilisateur_id,
                    'requete_id'     => $requete->id,
                    'message'        => $message,
                    'canal'          => 'app',
                    'est_lue'        => false,
                    'date_envoi'     => now(),
                ]);
            }
        });
    }

    public function etudiant(): BelongsTo
    {
        return $this->belongsTo(Etudiant::class);
    }

    public function cours(): BelongsTo
    {
        return $this->belongsTo(Cours::class);
    }

    public function session(): BelongsTo
    {
        return $this->belongsTo(SessionExamen::class, 'session_id');
    }

    public function agent(): BelongsTo
    {
        return $this->belongsTo(AgentScolarite::class, 'agent_id');
    }

    public function piecesJointes(): HasMany
    {
        return $this->hasMany(PieceJointe::class);
    }

    public function note(): HasOne
    {
        return $this->hasOne(Note::class);
    }

    public function notifications(): HasMany
    {
        return $this->hasMany(NotificationApp::class);
    }

    public function pvLignes(): HasMany
    {
        return $this->hasMany(PvRequeteLigne::class);
    }

    public function scopeEnAttente($query)
    {
        return $query->where('statut', StatutRequete::EnAttente->value);
    }

    public function scopeEnCoursVerification($query)
    {
        return $query->where('statut', StatutRequete::EnCoursVerification->value);
    }

    public function scopeFondee($query)
    {
        return $query->where('statut', StatutRequete::TraiteeFondee->value);
    }

    public function scopeNonFondee($query)
    {
        return $query->where('statut', StatutRequete::TraiteeNonFondee->value);
    }

    public function scopeTraitee($query)
    {
        return $query->whereIn('statut', [
            StatutRequete::TraiteeFondee->value,
            StatutRequete::TraiteeNonFondee->value,
        ]);
    }

    public function estDansUnPV(): bool
    {
        return $this->pvLignes()->exists();
    }
}
