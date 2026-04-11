<?php

namespace App\Services;

use App\Models\AgentScolarite;
use App\Models\HistoriqueAction;
use App\Models\PvRequete;
use App\Models\PvRequeteLigne;
use App\Models\Requete;
use App\Models\SessionExamen;
use Barryvdh\DomPDF\Facade\Pdf;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Storage;
use Symfony\Component\HttpFoundation\StreamedResponse;

class PVService
{
    public function generer(array $requeteIds, AgentScolarite $agent, SessionExamen $session, Request $request): PvRequete
    {
        $ref = $this->genererRefPV();

        $pv = PvRequete::create([
            'agent_id'       => $agent->id,
            'session_id'     => $session->id,
            'ref_pv'         => $ref,
            'date_generation' => now(),
        ]);

        $requetes = Requete::with(['etudiant.utilisateur', 'etudiant.filiere', 'cours', 'note'])
            ->whereIn('id', $requeteIds)
            ->get()
            ->sortBy(fn($r) => $r->etudiant->filiere->nom_filiere . $r->etudiant->utilisateur->nom);

        $ordre = 0;
        foreach ($requetes as $requete) {
            PvRequeteLigne::create([
                'pv_id'           => $pv->id,
                'requete_id'      => $requete->id,
                'ordre_affichage' => $ordre++,
            ]);
        }

        $pdf = Pdf::loadView('pdf.pv_requetes', [
            'pv'      => $pv,
            'requetes' => $requetes,
            'session' => $session,
            'agent'   => $agent,
        ])->setPaper('A4', 'landscape');

        $dossier = 'pv/';
        $nomFichier = $ref . '.pdf';
        Storage::disk('local')->put($dossier . $nomFichier, $pdf->output());

        $pv->update(['chemin_pdf' => $dossier . $nomFichier]);

        HistoriqueAction::create([
            'agent_id'    => $agent->id,
            'type_action' => 'generation_pv',
            'details'     => "Génération du PV {$ref} pour la session {$session->libelle}. " . count($requeteIds) . " requêtes incluses.",
            'ip_address'  => $request->ip(),
            'created_at'  => now(),
        ]);

        return $pv->fresh([
            'agent.utilisateur',
            'session',
            'lignes.requete.etudiant.utilisateur',
            'lignes.requete.cours',
            'lignes.requete.note',
        ]);
    }

    public function telecharger(PvRequete $pv): StreamedResponse
    {
        return Storage::disk('local')->download(
            $pv->chemin_pdf,
            $pv->ref_pv . '.pdf',
            ['Content-Type' => 'application/pdf']
        );
    }

    private function genererRefPV(): string
    {
        $date = now()->format('Ymd');
        $last = PvRequete::where('ref_pv', 'like', "PV-{$date}-%")->count();
        return 'PV-' . $date . '-' . str_pad($last + 1, 4, '0', STR_PAD_LEFT);
    }
}
