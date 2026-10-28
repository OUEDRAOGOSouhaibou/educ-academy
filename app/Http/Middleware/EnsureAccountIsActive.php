<?php

namespace App\Http\Middleware;

use Closure;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;
use Symfony\Component\HttpFoundation\Response;

class EnsureAccountIsActive
{
    /**
     * Vérifie le statut du compte et l'obligation de changement de mot de passe à chaque requête.
     *
     * @param  \Closure(\Illuminate\Http\Request): (\Symfony\Component\HttpFoundation\Response)  $next
     */
    public function handle(Request $request, Closure $next): Response
    {
        $user = Auth::user();

        if (! $user) {
            return $next($request);
        }

        // 1. Règle absolue de sécurité : Un utilisateur bloqué est déconnecté IMMÉDIATEMENT
        if ($user->isBlocked()) {
            Auth::logout();
            $request->session()->invalidate();
            $request->session()->regenerateToken();

            return redirect()->route('login')->withErrors([
                'phone' => 'Votre compte a été suspendu par l\'administrateur. Veuillez contacter votre professeur.',
            ]);
        }

        // 2. Compte en attente de validation (réservé aux élèves)
        if ($user->isPending()) {
            if (! $request->routeIs('student.pending', 'logout')) {
                return redirect()->route('student.pending');
            }
            return $next($request);
        }

        // 3. Changement obligatoire de mot de passe imposé par l'administrateur
        if ($user->must_change_password) {
            if (! $request->routeIs('password.change', 'password.change.update', 'logout')) {
                return redirect()->route('password.change')->with(
                    'warning',
                    'Pour des raisons de sécurité, vous devez définir un nouveau mot de passe avant d\'accéder à vos cours.'
                );
            }
        }

        return $next($request);
    }
}

