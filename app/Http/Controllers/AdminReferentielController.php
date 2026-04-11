<?php

namespace App\Http\Controllers;

use App\Models\Cours;
use App\Models\Departement;
use App\Models\Filiere;
use App\Models\SessionExamen;
use App\Services\NotificationService;
use Illuminate\Http\RedirectResponse;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;
use Illuminate\View\View;

class AdminReferentielController extends Controller
{
    public function __construct(private NotificationService $notificationService) {}

    public function index(Request $request): View
    {
        $notificationsCount = $this->notificationService->compterNonLues(Auth::user());
        $filieres  = Filiere::with('departement')->withCount('cours')->paginate(15);
        $sessions  = SessionExamen::orderByDesc('created_at')->paginate(15);
        $cours     = Cours::with('filiere')->paginate(15);
        return view('admin.referentiel.index', compact('filieres', 'sessions', 'cours', 'notificationsCount'));
    }

    public function create(): View
    {
        $departements = Departement::all();
        $filieres     = Filiere::all();
        $sessions     = SessionExamen::all();
        $notificationsCount = $this->notificationService->compterNonLues(Auth::user());
        return view('admin.referentiel.create', compact('departements', 'filieres', 'sessions', 'notificationsCount'));
    }

    public function store(Request $request): RedirectResponse
    {
        $type = $request->input('type');

        if ($type === 'filiere') {
            $request->validate([
                'departement_id' => ['required', 'exists:departements,id'],
                'nom_filiere'    => ['required', 'string', 'max:100'],
                'cycle'          => ['required', 'in:BTS,HND,LP'],
            ]);
            Filiere::create($request->only('departement_id', 'nom_filiere', 'cycle'));
        } elseif ($type === 'cours') {
            $request->validate([
                'filiere_id' => ['required', 'exists:filieres,id'],
                'nom_cours'  => ['required', 'string', 'max:150'],
                'niveau'     => ['required', 'integer', 'in:1,2'],
                'credits'    => ['nullable', 'integer'],
            ]);
            Cours::create($request->only('filiere_id', 'nom_cours', 'niveau', 'credits'));
        } elseif ($type === 'session') {
            $request->validate([
                'code'             => ['required', 'string', 'max:20', 'unique:sessions_examens,code'],
                'libelle'          => ['required', 'string', 'max:100'],
                'annee_acad'       => ['required', 'string', 'max:10'],
                'date_publication' => ['nullable', 'date'],
                'est_active'       => ['boolean'],
            ]);
            SessionExamen::create($request->only('code', 'libelle', 'annee_acad', 'date_publication', 'est_active'));
        }

        return redirect()->route('admin.filieres.index')->with('success', 'Élément créé avec succès.');
    }

    public function edit(int $id): View
    {
        $departements = Departement::all();
        $filieres     = Filiere::all();
        $notificationsCount = $this->notificationService->compterNonLues(Auth::user());
        return view('admin.referentiel.edit', compact('departements', 'filieres', 'notificationsCount'));
    }

    public function update(Request $request, int $id): RedirectResponse
    {
        return redirect()->route('admin.filieres.index')->with('success', 'Élément mis à jour.');
    }

    public function destroy(int $id): RedirectResponse
    {
        return redirect()->route('admin.filieres.index')->with('success', 'Élément supprimé.');
    }

    public function toggleSession(int $id): RedirectResponse
    {
        $session = SessionExamen::findOrFail($id);
        $session->update(['est_active' => ! $session->est_active]);

        $statut = $session->est_active ? 'activée' : 'désactivée';

        return redirect()->route('admin.filieres.index')
            ->with('success', "Session « {$session->libelle} » {$statut} avec succès.");
    }
}
