<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\HasMany;

class SessionExamen extends Model
{
    use HasFactory;

    protected $table = 'sessions_examens';

    protected $fillable = ['code', 'libelle', 'annee_acad', 'date_publication', 'est_active'];

    protected $casts = [
        'est_active'       => 'boolean',
        'date_publication' => 'datetime',
    ];

    public function requetes(): HasMany
    {
        return $this->hasMany(Requete::class, 'session_id');
    }

    public function pvRequetes(): HasMany
    {
        return $this->hasMany(PvRequete::class, 'session_id');
    }

    public function scopeActive($query)
    {
        return $query->where('est_active', true);
    }

    public function scopeOuverte($query)
    {
        return $query->where('est_active', true)
            ->whereNotNull('date_publication')
            ->where('date_publication', '>=', now()->subHours(72));
    }

    public function scopeExpiree($query)
    {
        return $query->where('est_active', true)
            ->whereNotNull('date_publication')
            ->where('date_publication', '<', now()->subHours(72));
    }

    public function fenetreOuverte(): bool
    {
        if (! $this->date_publication) {
            return false;
        }

        return now()->lte($this->date_publication->addHours(72));
    }

    public function tempsRestant(): ?int
    {
        if (! $this->date_publication) {
            return null;
        }

        $limite = $this->date_publication->addHours(72);

        return max(0, now()->diffInSeconds($limite, false));
    }
}
