<?php

namespace App\Http\Controllers;

use App\Http\Requests\ChangerStatutRequest;
use App\Http\Requests\GenererPVRequest;
use App\Enums\StatutRequete;
use App\Models\AgentScolarite;
use App\Models\Filiere;
use App\Models\HistoriqueAction;
use App\Models\NotificationApp;
use App\Models\PvRequete;
use App\Models\Requete;
use App\Models\SessionExamen;
use App\Services\NotificationService;
use App\Services\PVService;
use App\Services\RequeteService;
use Illuminate\Http\JsonResponse;
use Illuminate\Http\RedirectResponse;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\Hash;
use Illuminate\View\View;

class AgentController extends Controller
{
    public function __construct(
        private RequeteService $requeteService,
        private PVService $pvService,
        private NotificationService $notificationService
    ) {}

    private function agent(): AgentScolarite
    {
        return Auth::user()->agent;
    }

    public function dashboard(): View
    {
        $agent = $this->agent();

        $stats = [
            'total'      => Requete::count(),
            'en_attente' => Requete::enAttente()->count(),
            'en_cours'   => Requete::enCoursVerification()->count(),
            'traitees'   => Requete::traitee()->count(),
        ];

        $requetesPrioritaires = Requete::with(['etudiant.utilisateur', 'etudiant.filiere', 'cours'])
            ->enAttente()
            ->orderBy('date_soumission')
            ->limit(10)
            ->get();

        $mesRequetes = $agent->requetes()
            ->with(['etudiant.utilisateur', 'cours'])
            ->enCoursVerification()
            ->orderBy('date_prise_en_charge')
            ->limit(5)
            ->get();

        $notificationsCount = $this->notificationService->compterNonLues(Auth::user());

        return view('agent.dashboard', compact('stats', 'requetesPrioritaires', 'mesRequetes', 'notificationsCount', 'agent'));
    }

    public function fileRequetes(Request $request): View
    {
        $query = Requete::with(['etudiant.utilisateur', 'etudiant.filiere', 'cours', 'session', 'agent.utilisateur'])
            ->orderByDesc('date_soumission');

        if ($request->filled('statut')) {
            $query->where('statut', $request->statut);
        }
        if ($request->filled('filiere_id')) {
            $query->whereHas('etudiant', fn($q) => $q->where('filiere_id', $request->filiere_id));
        }
        if ($request->filled('date_debut')) {
            $query->where('date_soumission', '>=', $request->date_debut);
        }
        if ($request->filled('date_fin')) {
            $query->where('date_soumission', '<=', $request->date_fin . ' 23:59:59');
        }

        $requetes = $query->paginate(15)->withQueryString();
        $filieres = Filiere::all();
        $notificationsCount = $this->notificationService->compterNonLues(Auth::user());

        return view('agent.requetes.index', compact('requetes', 'filieres', 'notificationsCount'));
    }

    public function detail(int $id): View
    {
        $requete = Requete::with([
            'etudiant.utilisateur', 'etudiant.filiere',
            'cours', 'session', 'piecesJointes', 'note', 'agent.utilisateur',
        ])->findOrFail($id);

        $notificationsCount = $this->notificationService->compterNonLues(Auth::user());

        return view('agent.requetes.show', compact('requete', 'notificationsCount'));
    }

    public function prendreEnCharge(int $id, Request $request): RedirectResponse
    {
        $requete = Requete::findOrFail($id);
        $this->requeteService->prendreEnCharge($requete, $this->agent(), $request);

        return back()->with('success', 'Requête prise en charge avec succès.');
    }

    public function changerStatut(ChangerStatutRequest $request, int $id): JsonResponse|RedirectResponse
    {
        $requete = Requete::findOrFail($id);
        $data    = $request->validated();

        $this->requeteService->traiter(
            $requete,
            $data['decision'],
            $data['motif_rejet'] ?? null,
            isset($data['nouvelle_note']) ? (float) $data['nouvelle_note'] : null,
            $this->agent(),
            $request
        );

        if ($request->wantsJson()) {
            return response()->json(['success' => true, 'message' => 'Décision enregistrée avec succès.']);
        }

        return back()->with('success', 'La décision a été enregistrée et l\'étudiant notifié.');
    }

    public function formPV(): View
    {
        $sessions = SessionExamen::orderByDesc('annee_acad')->get();
        $filieres = Filiere::orderBy('nom_filiere')->get();
        $notificationsCount = $this->notificationService->compterNonLues(Auth::user());

        return view('agent.pv.create', compact('sessions', 'filieres', 'notificationsCount'));
    }

    public function apercu(Request $request): JsonResponse
    {
        $requetes = Requete::with(['etudiant.utilisateur', 'etudiant.filiere', 'cours', 'note'])
            ->where('session_id', $request->session_id)
            ->whereHas('etudiant', fn($q) => $q->where('filiere_id', $request->filiere_id))
            ->traitee()
            ->get();

        $fondees    = $requetes->filter(fn($r) => $r->statut === StatutRequete::TraiteeFondee)->count();
        $nonFondees = $requetes->filter(fn($r) => $r->statut === StatutRequete::TraiteeNonFondee)->count();

        return response()->json([
            'total'       => $requetes->count(),
            'fondees'     => $fondees,
            'non_fondees' => $nonFondees,
            'requetes'    => $requetes->map(fn($r) => [
                'ref'       => $r->ref_requete,
                'etudiant'  => $r->etudiant->utilisateur->nom_complet,
                'matricule' => $r->etudiant->matricule,
                'cours'     => $r->cours->nom_cours,
                'type'      => str_replace('_', ' ', $r->type_anomalie->value),
                'statut'    => $r->statut->value,
                'note_av'   => $r->note?->note_avant,
                'note_ap'   => $r->note?->note_apres,
            ]),
        ]);
    }

    public function genererPV(GenererPVRequest $request): View|RedirectResponse
    {
        $data    = $request->validated();
        $session = SessionExamen::findOrFail($data['session_id']);
        $filiere = Filiere::findOrFail($data['filiere_id']);

        $requeteIds = Requete::traitee()
            ->where('session_id', $session->id)
            ->whereHas('etudiant', fn($q) => $q->where('filiere_id', $filiere->id))
            ->pluck('id')
            ->toArray();

        if (empty($requeteIds)) {
            return back()->with('error', 'Aucune requête traitée trouvée pour cette session et filière.');
        }

        $pv = $this->pvService->generer($requeteIds, $this->agent(), $session, $request);

        $notificationsCount = $this->notificationService->compterNonLues(Auth::user());

        return view('agent.pv.show', compact('pv', 'notificationsCount'));
    }

    public function telechargerPV(int $id)
    {
        $pv = PvRequete::where('agent_id', $this->agent()->id)->findOrFail($id);
        return $this->pvService->telecharger($pv);
    }

    public function historique(Request $request): View
    {
        $query = HistoriqueAction::with(['agent.utilisateur', 'requete'])
            ->where('agent_id', $this->agent()->id)
            ->orderByDesc('created_at');

        $historiques = $query->paginate(20)->withQueryString();
        $notificationsCount = $this->notificationService->compterNonLues(Auth::user());

        return view('agent.historique', compact('historiques', 'notificationsCount'));
    }

    public function profil(): View
    {
        $user  = Auth::user();
        $agent = $this->agent();
        $notificationsCount = $this->notificationService->compterNonLues($user);

        return view('agent.profil', compact('user', 'agent', 'notificationsCount'));
    }

    public function updateProfil(Request $request): RedirectResponse
    {
        $request->validate([
            'password'              => ['required', 'string', 'min:8', 'confirmed'],
            'password_confirmation' => ['required'],
        ]);

        Auth::user()->update(['password' => Hash::make($request->password)]);

        return back()->with('success', 'Mot de passe mis à jour avec succès.');
    }

    public function countNotifications(): JsonResponse
    {
        return response()->json(['count' => $this->notificationService->compterNonLues(Auth::user())]);
    }
}
