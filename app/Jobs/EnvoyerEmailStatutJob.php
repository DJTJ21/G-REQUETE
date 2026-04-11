<?php

namespace App\Jobs;

use App\Mail\StatutChangeMail;
use App\Models\Requete;
use Illuminate\Bus\Queueable;
use Illuminate\Contracts\Queue\ShouldQueue;
use Illuminate\Foundation\Bus\Dispatchable;
use Illuminate\Queue\InteractsWithQueue;
use Illuminate\Queue\SerializesModels;
use Illuminate\Support\Facades\Mail;

class EnvoyerEmailStatutJob implements ShouldQueue
{
    use Dispatchable, InteractsWithQueue, Queueable, SerializesModels;

    public int $tries = 3;
    public int $backoff = 60;

    public function __construct(public readonly Requete $requete) {}

    public function handle(): void
    {
        $email = $this->requete->etudiant->utilisateur->email;
        Mail::to($email)->send(new StatutChangeMail($this->requete));
    }

    public function failed(\Throwable $e): void
    {
        \Log::error('EnvoyerEmailStatutJob failed for requete ' . $this->requete->ref_requete . ': ' . $e->getMessage());
    }
}
