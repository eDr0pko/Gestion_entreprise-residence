<?php

namespace App\Http\Middleware;

use Closure;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Http;
use Illuminate\Support\Facades\Log;

class VerifyNHSToken
{
    /**
     * Vérifie un token Sanctum via le backend NHS
     */
    public function handle(Request $request, Closure $next)
    {
        $token = $request->bearerToken();
        
        if (!$token) {
            return response()->json([
                'success' => false,
                'message' => 'Token d\'authentification requis'
            ], 401);
        }

        try {
            // Vérifier le token auprès du backend NHS
            $nhsApiUrl = rtrim(env('NHS_API_URL', 'http://localhost:8001'), '/');
            $response = Http::withHeaders([
                'Authorization' => 'Bearer ' . $token,
                'Accept' => 'application/json',
                'Content-Type' => 'application/json'
            ])->get($nhsApiUrl . '/api/user');

            if ($response->successful()) {
                $userData = $response->json();
                if (isset($userData['success']) && $userData['success']) {
                    // Stocker les informations utilisateur dans la requête
                    $request->attributes->set('nhs_user', $userData['user']);
                    return $next($request);
                }
            }

            Log::warning('Token verification failed', [
                'token' => substr($token, 0, 20) . '...',
                'response_status' => $response->status(),
                'response_body' => $response->body()
            ]);

            return response()->json([
                'success' => false,
                'message' => 'Token invalide ou expiré'
            ], 401);

        } catch (\Exception $e) {
            Log::error('NHS token verification error', [
                'message' => $e->getMessage(),
                'token' => substr($token, 0, 20) . '...'
            ]);

            return response()->json([
                'success' => false,
                'message' => 'Erreur de vérification d\'authentification'
            ], 500);
        }
    }
}
