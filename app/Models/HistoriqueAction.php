<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;

class HistoriqueAction extends Model
{
    protected $table = 'historique_actions';

    public $timestamps = false;

    protected $fillable = [
        'agent_id', 'admin_id', 'requete_id', 'type_action', 'details', 'ip_address', 'created_at',
    ];

    protected $casts = [
        'created_at' => 'datetime',
    ];

    public function agent(): BelongsTo
    {
        return $this->belongsTo(AgentScolarite::class, 'agent_id');
    }

    public function admin(): BelongsTo
    {
        return $this->belongsTo(Administrateur::class, 'admin_id');
    }

    public function requete(): BelongsTo
    {
        return $this->belongsTo(Requete::class);
    }
}
