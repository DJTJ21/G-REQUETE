<?php

namespace App\Http\Middleware;

use App\Enums\RoleUtilisateur;
use Closure;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;
use Symfony\Component\HttpFoundation\Response;

class EnsureIsEtudiant
{
    public function handle(Request $request, Closure $next): Response
    {
        if (! Auth::check() || Auth::user()->role !== RoleUtilisateur::Etudiant) {
            return redirect()->route('login')->with('error', 'Accès réservé aux étudiants.');
        }

        return $next($request);
    }
}
