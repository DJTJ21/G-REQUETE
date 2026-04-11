<?php

namespace App\Console\Commands;

use App\Models\SessionExamen;
use Illuminate\Console\Command;

class CloseExpiredSessions extends Command
{
    protected $signature   = 'sessions:close-expired';
    protected $description = 'Marque comme inactives les sessions dont la fenêtre de 72h est expirée';

    public function handle(): int
    {
        $count = SessionExamen::expiree()->update(['est_active' => false]);

        if ($count > 0) {
            $this->info("[sessions:close-expired] {$count} session(s) passée(s) à INACTIVE.");
        } else {
            $this->line('[sessions:close-expired] Aucune session expirée à fermer.');
        }

        return self::SUCCESS;
    }
}
