<?php

namespace App\Http\Controllers;

use App\Models\AgentScolarite;
use App\Models\Administrateur;
use App\Models\Etudiant;
use App\Models\Filiere;
use App\Models\HistoriqueAction;
use App\Models\User;
use App\Services\NotificationService;
use App\Services\StatistiquesService;
use Illuminate\Http\JsonResponse;
use Illuminate\Http\RedirectResponse;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\Hash;
use Illuminate\Support\Str;
use Illuminate\View\View;
use Symfony\Component\HttpFoundation\StreamedResponse;

class AdminController extends Controller
{
    public function __construct(
        private StatistiquesService $statsService,
        private NotificationService $notificationService
    ) {}

    public function dashboard(): View
    {
        $stats = $this->statsService->statsGlobales();
        $requetesParMois  = $this->statsService->requetesParMois(now()->year);
        $requetesParStatut = $this->statsService->requetesParStatut();
        $topAgents        = $this->statsService->topAgents();
        $derniersHistos   = HistoriqueAction::with(['agent.utilisateur', 'requete'])
            ->orderByDesc('created_at')->limit(10)->get();
        $notificationsCount = $this->notificationService->compterNonLues(Auth::user());

        return view('admin.dashboard', compact(
            'stats', 'requetesParMois', 'requetesParStatut', 'topAgents', 'derniersHistos', 'notificationsCount'
        ));
    }

    public function index(Request $request): View
    {
        $query = User::query();
        if ($request->filled('role'))   $query->where('role', $request->role);
        if ($request->filled('search')) {
            $query->where(fn($q) => $q->where('nom', 'like', '%'.$request->search.'%')
                ->orWhere('prenom', 'like', '%'.$request->search.'%')
                ->orWhere('email', 'like', '%'.$request->search.'%'));
        }
        $utilisateurs = $query->orderByDesc('created_at')->paginate(15)->withQueryString();
        $notificationsCount = $this->notificationService->compterNonLues(Auth::user());

        return view('admin.utilisateurs.index', compact('utilisateurs', 'notificationsCount'));
    }

    public function create(): View
    {
        $filieres = Filiere::with('departement')->get();
        $notificationsCount = $this->notificationService->compterNonLues(Auth::user());
        return view('admin.utilisateurs.create', compact('filieres', 'notificationsCount'));
    }

    public function store(Request $request): RedirectResponse
    {
        $request->validate([
            'nom'        => ['required', 'string', 'max:100'],
            'prenom'     => ['required', 'string', 'max:100'],
            'email'      => ['required', 'email', 'unique:users,email'],
            'role'       => ['required', 'in:etudiant,agent,admin'],
            'filiere_id' => ['required_if:role,etudiant', 'nullable', 'exists:filieres,id'],
            'niveau'     => ['required_if:role,etudiant', 'nullable', 'integer', 'in:1,2'],
            'matricule'        => ['required_if:role,etudiant', 'nullable', 'string', 'max:20', 'unique:etudiants,matricule'],
            'matricule_agent'  => ['required_if:role,agent', 'nullable', 'string', 'max:20', 'unique:agents_scolarite,matricule_agent'],
            'service'          => ['nullable', 'string', 'max:100'],
        ]);

        $motDePasse = Str::random(10);
        $user = User::create([
            'nom'      => $request->nom,
            'prenom'   => $request->prenom,
            'email'    => $request->email,
            'password' => Hash::make($motDePasse),
            'role'     => $request->role,
            'est_actif' => true,
        ]);

        match ($request->role) {
            'etudiant' => Etudiant::create([
                'utilisateur_id' => $user->id,
                'matricule'      => $request->matricule,
                'filiere_id'     => $request->filiere_id,
                'niveau'         => $request->niveau,
            ]),
            'agent' => AgentScolarite::create([
                'utilisateur_id' => $user->id,
                'matricule_agent' => $request->matricule_agent,
                'service'        => $request->service ?? 'Scolarité',
            ]),
            'admin' => Administrateur::create([
                'utilisateur_id' => $user->id,
                'niveau_acces'   => 1,
            ]),
        };

        HistoriqueAction::create([
            'admin_id'    => Auth::user()->administrateur->id,
            'type_action' => 'creation_compte',
            'details'     => "Création du compte {$request->role} pour {$user->nom_complet} ({$user->email}). Mot de passe temporaire généré.",
            'ip_address'  => $request->ip(),
            'created_at'  => now(),
        ]);

        return redirect()->route('admin.utilisateurs.index')
            ->with('success', "Compte créé pour {$user->nom_complet}. Mot de passe temporaire : {$motDePasse}");
    }

    public function edit(int $id): View
    {
        $utilisateur = User::with(['etudiant', 'agent', 'administrateur'])->findOrFail($id);
        $filieres    = Filiere::with('departement')->get();
        $notificationsCount = $this->notificationService->compterNonLues(Auth::user());

        return view('admin.utilisateurs.edit', compact('utilisateur', 'filieres', 'notificationsCount'));
    }

    public function update(Request $request, int $id): RedirectResponse
    {
        $user = User::findOrFail($id);
        $request->validate([
            'nom'    => ['required', 'string', 'max:100'],
            'prenom' => ['required', 'string', 'max:100'],
            'email'  => ['required', 'email', 'unique:users,email,' . $id],
        ]);

        $user->update($request->only('nom', 'prenom', 'email'));

        return redirect()->route('admin.utilisateurs.index')->with('success', 'Compte mis à jour.');
    }

    public function destroy(int $id): RedirectResponse
    {
        User::findOrFail($id)->delete();
        return redirect()->route('admin.utilisateurs.index')->with('success', 'Compte supprimé.');
    }

    public function toggleActif(int $id, Request $request): RedirectResponse
    {
        $user = User::findOrFail($id);
        $user->update(['est_actif' => ! $user->est_actif]);

        $etat = $user->est_actif ? 'activé' : 'désactivé';
        HistoriqueAction::create([
            'admin_id'    => Auth::user()->administrateur->id,
            'type_action' => 'toggle_compte',
            'details'     => "Compte de {$user->nom_complet} ({$user->email}) {$etat}.",
            'ip_address'  => $request->ip(),
            'created_at'  => now(),
        ]);

        return back()->with('success', "Compte {$etat} avec succès.");
    }

    public function journal(Request $request): View
    {
        $query = HistoriqueAction::with(['agent.utilisateur', 'admin.utilisateur', 'requete'])
            ->orderByDesc('created_at');

        if ($request->filled('date_debut')) $query->where('created_at', '>=', $request->date_debut);
        if ($request->filled('date_fin'))   $query->where('created_at', '<=', $request->date_fin . ' 23:59:59');
        if ($request->filled('type'))       $query->where('type_action', $request->type);
        if ($request->filled('agent_id'))   $query->where('agent_id', $request->agent_id);
        if ($request->filled('requete_ref')) {
            $query->whereHas('requete', fn($q) => $q->where('ref_requete', 'like', '%'.$request->requete_ref.'%'));
        }

        $historiques  = $query->paginate(20)->withQueryString();
        $agents       = AgentScolarite::with('utilisateur')->get();
        $notificationsCount = $this->notificationService->compterNonLues(Auth::user());
        $totalActions     = HistoriqueAction::count();
        $totalDecisions   = HistoriqueAction::where('type_action', 'decision_requete')->count();
        $totalPV          = HistoriqueAction::where('type_action', 'generation_pv')->count();

        return view('admin.journal.index', compact(
            'historiques', 'agents', 'notificationsCount',
            'totalActions', 'totalDecisions', 'totalPV'
        ));
    }

    public function statistiques(): View
    {
        $stats = $this->statsService->statsGlobales();
        $requetesParMois  = $this->statsService->requetesParMois(now()->year);
        $notificationsCount = $this->notificationService->compterNonLues(Auth::user());

        return view('admin.statistiques', compact('stats', 'requetesParMois', 'notificationsCount'));
    }

    public function exportJournal(Request $request): StreamedResponse
    {
        $historiques = HistoriqueAction::with(['agent.utilisateur', 'admin.utilisateur', 'requete'])
            ->orderByDesc('created_at')->get();

        $headers = ['Content-Type' => 'text/csv; charset=UTF-8'];

        return response()->streamDownload(function () use ($historiques) {
            $handle = fopen('php://output', 'w');
            fputs($handle, "\xEF\xBB\xBF");
            fputcsv($handle, ['Date', 'Type Action', 'Acteur', 'Requête', 'Détails', 'IP'], ';');

            foreach ($historiques as $h) {
                $acteur = $h->agent?->utilisateur?->nom_complet ?? $h->admin?->utilisateur?->nom_complet ?? 'Système';
                fputcsv($handle, [
                    $h->created_at->format('d/m/Y H:i'),
                    $h->type_action,
                    $acteur,
                    $h->requete?->ref_requete ?? '-',
                    $h->details,
                    $h->ip_address,
                ], ';');
            }
            fclose($handle);
        }, 'journal_' . now()->format('Ymd_His') . '.csv', $headers);
    }

    public function exportJournalPdf(Request $request)
    {
        $historiques = HistoriqueAction::with(['agent.utilisateur', 'admin.utilisateur', 'requete'])
            ->orderByDesc('created_at')
            ->limit(200)
            ->get();

        $pdf = \Barryvdh\DomPDF\Facade\Pdf::loadView('pdf.journal_audit', [
            'historiques' => $historiques,
            'generatedAt' => now(),
        ])->setPaper('A4', 'landscape');

        return $pdf->download('journal_audit_' . now()->format('Ymd_His') . '.pdf');
    }

    public function importForm(): View
    {
        $filieres = Filiere::with('departement')->orderBy('nom_filiere')->get();
        $notificationsCount = $this->notificationService->compterNonLues(Auth::user());
        return view('admin.utilisateurs.import', compact('filieres', 'notificationsCount'));
    }

    public function importCsv(Request $request): RedirectResponse
    {
        $request->validate([
            'fichier_csv' => ['required', 'file', 'mimes:csv,txt', 'max:2048'],
        ]);

        $handle = fopen($request->file('fichier_csv')->getRealPath(), 'r');
        $header = fgetcsv($handle, 0, ';');

        $header = array_map(fn($h) => strtolower(trim($h)), $header);
        $required = ['nom', 'prenom', 'email', 'matricule', 'filiere_id', 'niveau'];
        foreach ($required as $col) {
            if (!in_array($col, $header)) {
                fclose($handle);
                return back()->withErrors(['fichier_csv' => "Colonne manquante : {$col}. Colonnes attendues : " . implode(', ', $required)]);
            }
        }

        $created = 0;
        $errors  = [];
        $row     = 1;

        while (($line = fgetcsv($handle, 0, ';')) !== false) {
            $row++;
            if (count($line) < count($header)) continue;

            $data = array_combine($header, $line);
            $data = array_map('trim', $data);

            if (User::where('email', $data['email'])->exists()) {
                $errors[] = "Ligne {$row} : email {$data['email']} déjà utilisé.";
                continue;
            }
            if (Etudiant::where('matricule', $data['matricule'])->exists()) {
                $errors[] = "Ligne {$row} : matricule {$data['matricule']} déjà utilisé.";
                continue;
            }

            $motDePasse = Str::random(10);
            $user = User::create([
                'nom'      => $data['nom'],
                'prenom'   => $data['prenom'],
                'email'    => $data['email'],
                'password' => Hash::make($motDePasse),
                'role'     => 'etudiant',
                'est_actif' => true,
            ]);
            Etudiant::create([
                'utilisateur_id' => $user->id,
                'matricule'      => $data['matricule'],
                'filiere_id'     => $data['filiere_id'],
                'niveau'         => (int) $data['niveau'],
            ]);
            $created++;
        }
        fclose($handle);

        HistoriqueAction::create([
            'admin_id'    => Auth::user()->administrateur->id,
            'type_action' => 'import_csv',
            'details'     => "{$created} étudiant(s) importé(s) via CSV.",
            'ip_address'  => $request->ip(),
            'created_at'  => now(),
        ]);

        $message = "{$created} étudiant(s) importé(s) avec succès.";
        if ($errors) $message .= ' Erreurs : ' . implode(' | ', $errors);

        return redirect()->route('admin.utilisateurs.index')->with('success', $message);
    }

    public function countNotifications(): JsonResponse
    {
        return response()->json(['count' => $this->notificationService->compterNonLues(Auth::user())]);
    }
}
