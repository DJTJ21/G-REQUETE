<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;

class PvRequeteLigne extends Model
{
    use HasFactory;

    protected $table = 'pv_requete_lignes';

    protected $fillable = ['pv_id', 'requete_id', 'ordre_affichage'];

    public function pv(): BelongsTo
    {
        return $this->belongsTo(PvRequete::class, 'pv_id');
    }

    public function requete(): BelongsTo
    {
        return $this->belongsTo(Requete::class);
    }
}
