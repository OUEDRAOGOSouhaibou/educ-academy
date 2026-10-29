<?php

namespace App\Http\Middleware;

use Closure;
use Illuminate\Http\Request;
use Symfony\Component\HttpFoundation\Response;

class SecurityHeaders
{
    /**
     * Ajoute les en-têtes HTTP de sécurité recommandés par l'OWASP.
     *
     * @param  \Closure(\Illuminate\Http\Request): (\Symfony\Component\HttpFoundation\Response)  $next
     */
    public function handle(Request $request, Closure $next): Response
    {
        $response = $next($request);

        // Protection contre le Clickjacking (interdit l'intégration dans des iframes externes)
        $response->headers->set('X-Frame-Options', 'SAMEORIGIN');

        // Protection contre le reniflage de type MIME (MIME-sniffing)
        $response->headers->set('X-Content-Type-Options', 'nosniff');

        // Politique de référencement stricte (ne divulgue pas d'URL sensibles aux domaines tiers)
        $response->headers->set('Referrer-Policy', 'strict-origin-when-cross-origin');

        // Désactivation des API matérielles superflues
        $response->headers->set('Permissions-Policy', 'camera=(), microphone=(), geolocation=()');

        return $response;
    }
}
