<?php

namespace App\Http\Middleware;

use Illuminate\Auth\Middleware\Authenticate as Middleware;
use Illuminate\Http\Request;

class Authenticate extends Middleware
{
    /**
     * Get the path the user should be redirected to when they are not authenticated.
     */
    protected function redirectTo(Request $request): ?string
    {
        // Pour une API, on ne redirige pas, on retourne une erreur JSON
        if ($request->expectsJson() || $request->is('api/*')) {
            return null;
        }
        
        // Si ce n'est pas une requête API, on peut rediriger vers une page de login
        // Mais dans votre cas, toutes les requêtes sont API
        return null;
    }
    
    /**
     * Handle an unauthenticated user.
     */
    protected function unauthenticated($request, array $guards)
    {
        if ($request->expectsJson() || $request->is('api/*')) {
            abort(response()->json([
                'success' => false,
                'message' => 'Non authentifié.',
                'error' => 'Unauthenticated'
            ], 401));
        }
        
        // Pour les requêtes web (qui n'existent pas dans votre cas)
        throw new \Illuminate\Auth\AuthenticationException(
            'Unauthenticated.', $guards, $this->redirectTo($request)
        );
    }
}
