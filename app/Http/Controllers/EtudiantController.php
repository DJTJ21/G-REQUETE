<?php

namespace App\Http\Controllers;

use App\Enums\StatutRequete;
use App\Exceptions\FenetreDepasseeException;
use App\Http\Requests\SoumettreRequeteRequest;
use App\Models\Cours;
use App\Models\Filiere;
use App\Models\NotificationApp;
use App\Models\Requete;
use App\Models\SessionExamen;
use App\Services\NotificationService;
use App\Services\RequeteService;
use Illuminate\Http\JsonResponse;
use Illuminate\Http\RedirectResponse;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\Hash;
use Illuminate\View\View;

class EtudiantController extends Controller
{
    public function __construct(
        private RequeteService $requeteService,
        private NotificationService $notificationService
    ) {}

    public function dashboard(): View
    {
        $user     = Auth::user();
        $etudiant = $user->etudiant;

        $stats = [
            'total'             => $etudiant->requetes()->count(),
            'en_attente'        => $etudiant->requetes()->enAttente()->count(),
            'en_cours'          => $etudiant->requetes()->enCoursVerification()->count(),
            'traitees'          => $etudiant->requetes()->traitee()->count(),
            'taux_traitement'   => $etudiant->requetes()->count() > 0
                ? round(($etudiant->requetes()->traitee()->count() / $etudiant->requetes()->count()) * 100)
                : 0,
        ];

        $requetesRecentes = $etudiant->requetes()
            ->with(['cours', 'session'])
            ->orderByDesc('created_at')
            ->limit(5)
            ->get();

        $notificationsCount = $this->notificationService->compterNonLues($user);

        return view('etudiant.dashboard', compact('user', 'etudiant', 'stats', 'requetesRecentes', 'notificationsCount'));
    }

    public function mesRequetes(Request $request): View
    {
        $user     = Auth::user();
        $etudiant = $user->etudiant;

        $query = $etudiant->requetes()->with(['cours', 'session'])->orderByDesc('created_at');

        if ($request->filled('statut')) {
            $query->where('statut', $request->statut);
        }

        $requetes = $query->paginate(15)->withQueryString();
        $notificationsCount = $this->notificationService->compterNonLues($user);

        return view('etudiant.requetes.index', compact('requetes', 'notificationsCount'));
    }

    public function formulaire(): View|RedirectResponse
    {
        $sessions = SessionExamen::ouverte()->orderByDesc('date_publication')->get();

        if ($sessions->isEmpty()) {
            return redirect()->route('etudiant.dashboard')
                ->with('error', 'Aucune session en cours de soumission. Revenez lorsqu\'une session sera publiée.');
        }

        $notificationsCount = $this->notificationService->compterNonLues(Auth::user());

        return view('etudiant.requetes.create', compact('sessions', 'notificationsCount'));
    }

    public function soumettre(SoumettreRequeteRequest $request): RedirectResponse
    {
        try {
            $etudiant = Auth::user()->etudiant;
            $this->requeteService->soumettre($request->validated(), $etudiant, $request);

            return redirect()->route('etudiant.requetes.index')
                ->with('success', 'Votre requête a été soumise avec succès. Vous recevrez une notification dès sa prise en charge.');
        } catch (FenetreDepasseeException $e) {
            return back()->with('error', $e->getMessage());
        } catch (\Illuminate\Database\UniqueConstraintViolationException) {
            return back()->with('error', 'Vous avez déjà soumis une requête pour ce cours dans cette session.');
        }
    }

    public function detail(int $id): View|RedirectResponse
    {
        $etudiant = Auth::user()->etudiant;
        $requete  = Requete::with(['cours.filiere', 'session', 'piecesJointes', 'note', 'agent.utilisateur'])
            ->where('etudiant_id', $etudiant->id)
            ->findOrFail($id);

        $notificationsCount = $this->notificationService->compterNonLues(Auth::user());

        return view('etudiant.requetes.show', compact('requete', 'notificationsCount'));
    }

    public function notifications(): View
    {
        $user = Auth::user();
        $notifications = NotificationApp::where('utilisateur_id', $user->id)
            ->orderByDesc('date_envoi')
            ->paginate(20);

        $notificationsCount = $this->notificationService->compterNonLues($user);

        return view('etudiant.notifications', compact('notifications', 'notificationsCount'));
    }

    public function marquerLue(int $id): RedirectResponse
    {
        NotificationApp::where('utilisateur_id', Auth::id())
            ->findOrFail($id)
            ->update(['est_lue' => true]);

        return back();
    }

    public function marquerToutLu(): RedirectResponse
    {
        NotificationApp::where('utilisateur_id', Auth::id())
            ->where('est_lue', false)
            ->update(['est_lue' => true]);

        return back()->with('success', 'Toutes les notifications ont été marquées comme lues.');
    }

    public function profil(): View
    {
        $user     = Auth::user();
        $etudiant = $user->etudiant->load('filiere.departement');
        $notificationsCount = $this->notificationService->compterNonLues($user);

        return view('etudiant.profil', compact('user', 'etudiant', 'notificationsCount'));
    }

    public function updateProfil(Request $request): RedirectResponse
    {
        $request->validate(['password' => ['nullable', 'string', 'min:8', 'confirmed']]);

        if ($request->filled('password')) {
            Auth::user()->update(['password' => Hash::make($request->password)]);
        }

        return back()->with('success', 'Profil mis à jour avec succès.');
    }

    public function filieresByCycle(Request $request): JsonResponse
    {
        $cycle    = $request->query('cycle');
        $filieres = Filiere::where('cycle', $cycle)->get(['id', 'nom_filiere']);

        return response()->json($filieres);
    }

    public function coursByFiliereNiveau(Request $request): JsonResponse
    {
        $cours = Cours::where('filiere_id', $request->query('filiere_id'))
            ->where('niveau', $request->query('niveau'))
            ->get(['id', 'nom_cours']);

        return response()->json($cours);
    }

    public function countNotifications(): JsonResponse
    {
        $count = $this->notificationService->compterNonLues(Auth::user());
        return response()->json(['count' => $count]);
    }
}
