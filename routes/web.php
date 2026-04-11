<?php

use App\Http\Controllers\AuthController;
use App\Http\Controllers\EtudiantController;
use App\Http\Controllers\AgentController;
use App\Http\Controllers\AdminController;
use App\Http\Controllers\AdminReferentielController;
use Illuminate\Support\Facades\Route;

// Public
Route::get('/', fn() => redirect()->route('login'));
Route::get('/login',  [AuthController::class, 'showLogin'])->name('login');
Route::post('/login', [AuthController::class, 'login'])->name('login.post');
Route::post('/logout',[AuthController::class, 'logout'])->name('logout');

// ─── ÉTUDIANT ────────────────────────────────────────────────────────────────
Route::prefix('etudiant')->middleware(['auth', 'etudiant'])->name('etudiant.')->group(function () {
    Route::get('/dashboard',             [EtudiantController::class, 'dashboard'])->name('dashboard');
    Route::get('/requetes',              [EtudiantController::class, 'mesRequetes'])->name('requetes.index');
    Route::get('/requetes/nouvelle',     [EtudiantController::class, 'formulaire'])->name('requetes.create');
    Route::post('/requetes',             [EtudiantController::class, 'soumettre'])->name('requetes.store');
    Route::get('/requetes/{id}',         [EtudiantController::class, 'detail'])->name('requetes.show');
    Route::get('/notifications',         [EtudiantController::class, 'notifications'])->name('notifications.index');
    Route::patch('/notifications/{id}/lue', [EtudiantController::class, 'marquerLue'])->name('notifications.lue');
    Route::patch('/notifications/tout-lue',  [EtudiantController::class, 'marquerToutLu'])->name('notifications.tout-lue');
    Route::get('/profil',                [EtudiantController::class, 'profil'])->name('profil');
    Route::patch('/profil',              [EtudiantController::class, 'updateProfil'])->name('profil.update');

    // API JSON endpoints
    Route::get('/api/filieres',          [EtudiantController::class, 'filieresByCycle'])->name('api.filieres');
    Route::get('/api/cours',             [EtudiantController::class, 'coursByFiliereNiveau'])->name('api.cours');
    Route::get('/api/notifications/count', [EtudiantController::class, 'countNotifications'])->name('api.notifications.count');
});

// ─── AGENT ───────────────────────────────────────────────────────────────────
Route::prefix('agent')->middleware(['auth', 'agent'])->name('agent.')->group(function () {
    Route::get('/dashboard',                             [AgentController::class, 'dashboard'])->name('dashboard');
    Route::get('/requetes',                              [AgentController::class, 'fileRequetes'])->name('requetes.index');
    Route::get('/requetes/{id}',                         [AgentController::class, 'detail'])->name('requetes.show');
    Route::patch('/requetes/{id}/prendre-en-charge',     [AgentController::class, 'prendreEnCharge'])->name('requetes.prendre-en-charge');
    Route::patch('/requetes/{id}/statut',                [AgentController::class, 'changerStatut'])->name('requetes.statut');
    Route::get('/pv/creer',                              [AgentController::class, 'formPV'])->name('pv.create');
    Route::get('/pv/apercu',                             [AgentController::class, 'apercu'])->name('pv.apercu');
    Route::post('/pv',                                   [AgentController::class, 'genererPV'])->name('pv.store');
    Route::get('/pv/{id}/telecharger',                   [AgentController::class, 'telechargerPV'])->name('pv.download');
    Route::get('/historique',                            [AgentController::class, 'historique'])->name('historique');
    Route::get('/profil',                                [AgentController::class, 'profil'])->name('profil');
    Route::patch('/profil',                              [AgentController::class, 'updateProfil'])->name('profil.update');
    Route::get('/api/notifications/count',               [AgentController::class, 'countNotifications'])->name('api.notifications.count');
});

// ─── ADMIN ───────────────────────────────────────────────────────────────────
Route::prefix('admin')->middleware(['auth', 'admin'])->name('admin.')->group(function () {
    Route::get('/dashboard',                         [AdminController::class, 'dashboard'])->name('dashboard');
    Route::get('/utilisateurs',                      [AdminController::class, 'index'])->name('utilisateurs.index');
    Route::get('/utilisateurs/creer',                [AdminController::class, 'create'])->name('utilisateurs.create');
    Route::post('/utilisateurs',                     [AdminController::class, 'store'])->name('utilisateurs.store');
    Route::get('/utilisateurs/import',               [AdminController::class, 'importForm'])->name('utilisateurs.import');
    Route::post('/utilisateurs/import',              [AdminController::class, 'importCsv'])->name('utilisateurs.import.store');
    Route::get('/utilisateurs/{id}/modifier',        [AdminController::class, 'edit'])->name('utilisateurs.edit');
    Route::put('/utilisateurs/{id}',                 [AdminController::class, 'update'])->name('utilisateurs.update');
    Route::delete('/utilisateurs/{id}',              [AdminController::class, 'destroy'])->name('utilisateurs.destroy');
    Route::patch('/utilisateurs/{id}/toggle-actif',  [AdminController::class, 'toggleActif'])->name('utilisateurs.toggle-actif');
    Route::get('/journal',                           [AdminController::class, 'journal'])->name('journal.index');
    Route::get('/statistiques',                      [AdminController::class, 'statistiques'])->name('statistiques');
    Route::get('/export/journal',                    [AdminController::class, 'exportJournal'])->name('export.journal');
    Route::get('/export/journal/pdf',                [AdminController::class, 'exportJournalPdf'])->name('export.journal.pdf');

    // Référentiels
    Route::resource('/filieres', AdminReferentielController::class)->parameters(['filieres' => 'id'])->names('filieres');
    Route::resource('/cours', AdminReferentielController::class)->parameters(['cours' => 'id'])->names('cours');
    Route::resource('/sessions', AdminReferentielController::class)->parameters(['sessions' => 'id'])->names('sessions');
    Route::patch('/sessions/{id}/toggle-actif', [AdminReferentielController::class, 'toggleSession'])->name('sessions.toggle-actif');
    Route::get('/api/notifications/count', [AdminController::class, 'countNotifications'])->name('api.notifications.count');
});
