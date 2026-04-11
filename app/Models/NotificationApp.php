<?php

namespace App\Models;

use App\Enums\CanalNotification;
use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;

class NotificationApp extends Model
{
    use HasFactory;

    protected $table = 'notifications_app';

    protected $fillable = [
        'utilisateur_id', 'requete_id', 'message', 'canal', 'est_lue', 'date_envoi',
    ];

    protected $casts = [
        'est_lue'    => 'boolean',
        'date_envoi' => 'datetime',
        'canal'      => CanalNotification::class,
    ];

    public function utilisateur(): BelongsTo
    {
        return $this->belongsTo(User::class, 'utilisateur_id');
    }

    public function requete(): BelongsTo
    {
        return $this->belongsTo(Requete::class);
    }

    public function scopeNonLues($query)
    {
        return $query->where('est_lue', false);
    }

    public function scopeParUtilisateur($query, int $userId)
    {
        return $query->where('utilisateur_id', $userId);
    }
}
