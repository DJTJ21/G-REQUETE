<?php

namespace App\Services;

use App\Enums\StatutRequete;
use App\Exceptions\FenetreDepasseeException;
use App\Models\AgentScolarite;
use App\Models\Etudiant;
use App\Models\HistoriqueAction;
use App\Models\Note;
use App\Models\Requete;
use App\Models\SessionExamen;
use App\Jobs\EnvoyerEmailRequeteJob;
use App\Jobs\EnvoyerEmailStatutJob;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\DB;

class RequeteService
{
    public function __construct(
        private NotificationService $notificationService,
        private FichierService $fichierService
    ) {}

    public function soumettre(array $data, Etudiant $etudiant, Request $request): Requete
    {
        $session = SessionExamen::findOrFail($data['session_id']);

        if (! $session->fenetreOuverte()) {
            throw new FenetreDepasseeException('Le délai de soumission de 72h après la publication des résultats est dépassé.');
        }

        return DB::transaction(function () use ($data, $etudiant, $request, $session) {
            $requete = Requete::create([
                'ref_requete'     => $this->genererReference(),
                'etudiant_id'     => $etudiant->id,
                'cours_id'        => $data['cours_id'],
                'session_id'      => $data['session_id'],
                'type_anomalie'   => $data['type_anomalie'],
                'description'     => $data['description'] ?? null,
                'statut'          => StatutRequete::EnAttente->value,
                'date_soumission' => now(),
            ]);

            if (! empty($data['pieces_jointes'])) {
                foreach ($data['pieces_jointes'] as $fichier) {
                    $this->fichierService->sauvegarder($fichier, $requete);
                }
            }

            $this->notificationService->notifier(
                $etudiant->utilisateur,
                $requete,
                "Votre requête {$requete->ref_requete} a bien été soumise. Elle est en cours d'examen."
            );

            HistoriqueAction::create([
                'requete_id'  => $requete->id,
                'type_action' => 'soumission_requete',
                'details'     => "Soumission de la requête {$requete->ref_requete} par l'étudiant {$etudiant->matricule}.",
                'ip_address'  => $request->ip(),
                'created_at'  => now(),
            ]);

            EnvoyerEmailRequeteJob::dispatch($requete)->onQueue('emails');

            return $requete;
        });
    }

    public function prendreEnCharge(Requete $requete, AgentScolarite $agent, Request $request): Requete
    {
        $requete->update([
            'statut'               => StatutRequete::EnCoursVerification->value,
            'agent_id'             => $agent->id,
            'date_prise_en_charge' => now(),
        ]);

        $this->notificationService->notifier(
            $requete->etudiant->utilisateur,
            $requete,
            "Votre requête {$requete->ref_requete} a été prise en charge par un agent de scolarité."
        );

        HistoriqueAction::create([
            'agent_id'    => $agent->id,
            'requete_id'  => $requete->id,
            'type_action' => 'prise_en_charge',
            'details'     => "Prise en charge de la requête {$requete->ref_requete} par l'agent {$agent->utilisateur->nom_complet}.",
            'ip_address'  => $request->ip(),
            'created_at'  => now(),
        ]);

        return $requete->fresh();
    }

    public function traiter(
        Requete $requete,
        string $decision,
        ?string $motif,
        ?float $nouvelleNote,
        AgentScolarite $agent,
        Request $request
    ): Requete {
        $requete->update([
            'statut'          => $decision,
            'motif_rejet'     => $motif,
            'date_traitement' => now(),
        ]);

        if ($decision === StatutRequete::TraiteeFondee->value && $nouvelleNote !== null) {
            Note::updateOrCreate(
                ['requete_id' => $requete->id],
                [
                    'etudiant_id'       => $requete->etudiant_id,
                    'cours_id'          => $requete->cours_id,
                    'note_avant'        => null,
                    'note_apres'        => $nouvelleNote,
                    'modifie_par'       => $agent->id,
                    'date_modification' => now(),
                ]
            );
        }

        $statutLabel = StatutRequete::from($decision)->label();
        $this->notificationService->notifier(
            $requete->etudiant->utilisateur,
            $requete,
            "Votre requête {$requete->ref_requete} a été traitée : {$statutLabel}."
        );

        HistoriqueAction::create([
            'agent_id'    => $agent->id,
            'requete_id'  => $requete->id,
            'type_action' => 'decision_requete',
            'details'     => "Décision '{$statutLabel}' rendue sur {$requete->ref_requete}." . ($motif ? " Motif: {$motif}" : ''),
            'ip_address'  => $request->ip(),
            'created_at'  => now(),
        ]);

        EnvoyerEmailStatutJob::dispatch($requete->fresh())->onQueue('emails');

        return $requete->fresh();
    }

    public function genererReference(): string
    {
        $date = now()->format('Ymd');
        $last = Requete::where('ref_requete', 'like', "REQ-{$date}-%")->count();
        return 'REQ-' . $date . '-' . str_pad($last + 1, 4, '0', STR_PAD_LEFT);
    }
}
