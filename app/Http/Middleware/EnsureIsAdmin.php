<?php

namespace App\Http\Middleware;

use App\Enums\RoleUtilisateur;
use Closure;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;
use Symfony\Component\HttpFoundation\Response;

class EnsureIsAdmin
{
    public function handle(Request $request, Closure $next): Response
    {
        if (! Auth::check() || Auth::user()->role !== RoleUtilisateur::Admin) {
            return redirect()->route('login')->with('error', 'Accès réservé aux administrateurs.');
        }

        return $next($request);
    }
}
