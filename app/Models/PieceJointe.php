<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;

class PieceJointe extends Model
{
    use HasFactory;

    protected $table = 'pieces_jointes';

    protected $fillable = ['requete_id', 'nom_fichier', 'chemin_fichier', 'type_mime', 'taille'];

    public function requete(): BelongsTo
    {
        return $this->belongsTo(Requete::class);
    }

    public function getTailleHumaineAttribute(): string
    {
        $bytes = $this->taille;
        if ($bytes < 1024) return $bytes . ' o';
        if ($bytes < 1048576) return round($bytes / 1024, 1) . ' Ko';
        return round($bytes / 1048576, 1) . ' Mo';
    }

    public function isImage(): bool
    {
        return in_array($this->type_mime, ['image/jpeg', 'image/png', 'image/jpg']);
    }

    public function isPdf(): bool
    {
        return $this->type_mime === 'application/pdf';
    }
}
