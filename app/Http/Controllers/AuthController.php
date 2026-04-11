<?php

namespace App\Http\Controllers;

use App\Enums\RoleUtilisateur;
use App\Models\AgentScolarite;
use App\Models\Etudiant;
use App\Models\User;
use Illuminate\Http\RedirectResponse;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\Hash;
use Illuminate\Support\Facades\RateLimiter;
use Illuminate\View\View;

class AuthController extends Controller
{
    public function showLogin(): View
    {
        return view('auth.login');
    }

    public function login(Request $request): RedirectResponse
    {
        $request->validate([
            'email'    => ['required', 'string'],
            'password' => ['required'],
        ]);

        $key = 'login.' . $request->ip();

        if (RateLimiter::tooManyAttempts($key, 5)) {
            $seconds = RateLimiter::availableIn($key);
            return back()->withErrors(['email' => "Trop de tentatives. Réessayez dans {$seconds} secondes."])->withInput();
        }

        $identifiant = trim($request->email);
        $password    = $request->password;
        $remember    = $request->boolean('remember');

        $user = $this->trouverUtilisateur($identifiant);

        if (! $user || ! Hash::check($password, $user->password)) {
            RateLimiter::hit($key, 60);
            return back()->withErrors(['email' => 'Identifiants incorrects ou expirés.'])->withInput($request->only('email'));
        }

        Auth::login($user, $remember);

        RateLimiter::clear($key);

        $user = Auth::user();

        if (! $user->est_actif) {
            Auth::logout();
            return back()->withErrors(['email' => 'Votre compte a été désactivé. Contactez la scolarité.'])->withInput($request->only('email'));
        }

        $user->update(['derniere_connexion' => now()]);
        $request->session()->regenerate();

        return match ($user->role) {
            RoleUtilisateur::Etudiant => redirect()->route('etudiant.dashboard'),
            RoleUtilisateur::Agent    => redirect()->route('agent.dashboard'),
            RoleUtilisateur::Admin    => redirect()->route('admin.dashboard'),
        };
    }

    private function trouverUtilisateur(string $identifiant): ?User
    {
        if (filter_var($identifiant, FILTER_VALIDATE_EMAIL)) {
            return User::where('email', $identifiant)->first();
        }

        $etudiant = Etudiant::where('matricule', $identifiant)->first();
        if ($etudiant) return $etudiant->utilisateur;

        $agent = AgentScolarite::where('matricule_agent', $identifiant)->first();
        if ($agent) return $agent->utilisateur;

        return null;
    }

    public function logout(Request $request): RedirectResponse
    {
        Auth::logout();
        $request->session()->invalidate();
        $request->session()->regenerateToken();

        return redirect()->route('login');
    }
}
