<?php

namespace App\Services;

use App\Models\NotificationApp;
use App\Models\Requete;
use App\Models\User;

class NotificationService
{
    public function notifier(User $destinataire, ?Requete $requete, string $message): NotificationApp
    {
        return NotificationApp::create([
            'utilisateur_id' => $destinataire->id,
            'requete_id'     => $requete?->id,
            'message'        => $message,
            'canal'          => 'app',
            'est_lue'        => false,
            'date_envoi'     => now(),
        ]);
    }

    public function compterNonLues(User $utilisateur): int
    {
        return NotificationApp::where('utilisateur_id', $utilisateur->id)
            ->where('est_lue', false)
            ->count();
    }
}
