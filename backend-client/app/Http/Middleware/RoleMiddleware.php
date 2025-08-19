<?php
    namespace App\Http\Middleware;

    use Closure;
    use Illuminate\Http\Request;

    class RoleMiddleware
    {
        public function handle(Request $request, Closure $next, ...$roles)
        {
            if (!$request->user()) {
                return response()->json([
                    'success' => false,
                    'message' => 'Authentification requise'
                ], 401);
            }

            $userRole = $request->user()->getRole();
            
            if (!in_array($userRole, $roles)) {
                return response()->json([
                    'success' => false,
                    'message' => 'Accès non autorisé - Rôle requis: ' . implode(' ou ', $roles)
                ], 403);
            }

            return $next($request);
        }
    }
?>


