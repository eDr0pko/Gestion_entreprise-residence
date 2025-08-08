<?php

    namespace App\Http\Middleware;

    use Closure;
    use Illuminate\Http\Request;

    class DisableCsrfForApi
    {
        public function handle(Request $request, Closure $next)
        {
            // Désactiver la vérification CSRF pour toutes les routes API
            return $next($request);
        }
    }
?>


