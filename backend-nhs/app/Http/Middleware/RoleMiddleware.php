<?php
    namespace App\Http\Middleware;

    use Closure;
    use Illuminate\Http\Request;

    class RoleMiddleware
    {
        public function handle(Request $request, Closure $next, $role)
        {
            if (!$request->user() || $request->user()->getRole() !== $role) {
                return response()->json([
                    'success' => false,
                    'message' => 'Accès non autorisé'
                ], 403);
            }

            return $next($request);
        }
    }
?>


