<?php

namespace App\Http\Controllers;

use Illuminate\Http\Request;
use Illuminate\Support\Facades\Http;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\Log;

/**
 * Contrôleur proxy unifié pour rediriger toutes les requêtes
 * vers le backend NHS qui contient toute la logique métier
 */
class UnifiedProxyController extends Controller
{
    private $nhsApiUrl;

    public function __construct()
    {
        $this->nhsApiUrl = rtrim(env('NHS_API_URL', 'http://localhost:8001'), '/');
    }

    /**
     * Gestion unifiée de toutes les requêtes API
     */
    public function handle(Request $request, $path = '')
    {
        try {
            // Construction de l'URL complète
            $fullUrl = $this->nhsApiUrl . '/api/' . ltrim($path, '/');
            
            // Préparation des headers
            $headers = $this->prepareHeaders($request);
            
            // Préparation des données
            $data = $this->prepareData($request);
            
            // Log pour debugging
            Log::info('Proxy Request', [
                'method' => $request->method(),
                'original_url' => $request->fullUrl(),
                'proxy_url' => $fullUrl,
                'headers' => $headers,
                'user_id' => Auth::id()
            ]);

            // Envoi de la requête vers NHS
            if ($request->hasFile('avatar') || $request->hasFile('file')) {
                // Pour les uploads de fichiers, utiliser attach pour multipart/form-data
                $httpRequest = Http::withHeaders($headers)->timeout(30);
                
                if ($request->hasFile('avatar')) {
                    $httpRequest = $httpRequest->attach(
                        'avatar',
                        fopen($request->file('avatar')->getRealPath(), 'r'),
                        $request->file('avatar')->getClientOriginalName()
                    );
                }
                
                if ($request->hasFile('file')) {
                    $httpRequest = $httpRequest->attach(
                        'file',
                        fopen($request->file('file')->getRealPath(), 'r'),
                        $request->file('file')->getClientOriginalName()
                    );
                }
                
                // Ajouter les autres données du formulaire
                foreach ($request->except(['avatar', 'file']) as $key => $value) {
                    $httpRequest = $httpRequest->attach($key, $value);
                }
                
                $response = $httpRequest->{strtolower($request->method())}($fullUrl);
            } else {
                // Pour les requêtes JSON normales
                $response = Http::withHeaders($headers)
                    ->timeout(30)
                    ->{strtolower($request->method())}($fullUrl, $data);
            }

            // Correction pour la route /login : adapter le format du token
            if (strtolower($request->method()) === 'post' && preg_match('/login$/', $path)) {
                $json = json_decode($response->body(), true);
                if (is_array($json)) {
                    // Si le backend-nhs retourne 'auth_token', le renommer en 'token'
                    if (isset($json['auth_token']) && !isset($json['token'])) {
                        $json['token'] = $json['auth_token'];
                    }
                    // Si le backend-nhs retourne 'token', on laisse tel quel
                    return response()->json($json, $response->status())
                        ->withHeaders($this->filterResponseHeaders($response->headers()));
                }
            }
            // Retour de la réponse par défaut
            return response($response->body(), $response->status())
                ->withHeaders($this->filterResponseHeaders($response->headers()));

        } catch (\Exception $e) {
            Log::error('Proxy Error', [
                'message' => $e->getMessage(),
                'url' => $fullUrl ?? 'N/A',
                'method' => $request->method()
            ]);

            return response()->json([
                'success' => false,
                'message' => 'Erreur de communication avec le service NHS',
                'error' => env('APP_DEBUG') ? $e->getMessage() : 'Service temporairement indisponible'
            ], 500);
        }
    }

    /**
     * Préparation des headers pour la requête NHS
     */
    private function prepareHeaders(Request $request): array
    {
        $headers = [
            'Accept' => 'application/json',
            'X-Forwarded-For' => $request->ip(),
            'X-Original-Host' => $request->getHost(),
        ];

        // Ne pas forcer application/json pour les uploads de fichiers
        if (!$request->hasFile('avatar') && !$request->hasFile('file')) {
            $headers['Content-Type'] = 'application/json';
        }

        // Transfert de l'authentification
        if ($request->bearerToken()) {
            $headers['Authorization'] = 'Bearer ' . $request->bearerToken();
        }

        // Transfert de l'utilisateur connecté
        if (Auth::check()) {
            $headers['X-User-Id'] = Auth::id();
            $headers['X-User-Email'] = Auth::user()->email;
            $headers['X-User-Role'] = method_exists(Auth::user(), 'getRole') ? Auth::user()->getRole() : 'resident';
        }

        return $headers;
    }

    /**
     * Préparation des données pour la requête NHS
     */
    private function prepareData(Request $request): array
    {
        // Pour les uploads de fichiers, utiliser la requête directement
        if ($request->hasFile('avatar') || $request->hasFile('file')) {
            return $request->all();
        }

        $data = $request->all();
        
        // Ajout d'informations contextuelles seulement pour les requêtes JSON
        $data['_client_context'] = [
            'timestamp' => now()->toISOString(),
            'user_agent' => $request->userAgent(),
            'ip' => $request->ip(),
        ];

        return $data;
    }

    /**
     * Filtrage des headers de réponse
     */
    private function filterResponseHeaders(array $headers): array
    {
        $allowedHeaders = [
            'content-type',
            'cache-control',
            'expires',
            'last-modified',
            'etag',
            'x-ratelimit-limit',
            'x-ratelimit-remaining',
        ];

        return array_intersect_key($headers, array_flip($allowedHeaders));
    }

    /**
     * Gestion spécialisée pour l'authentification
     */
    public function handleAuth(Request $request, $action)
    {
        // Pour l'authentification, on peut avoir une logique spéciale
        // comme la gestion des tokens, sessions, etc.
        return $this->handle($request, "auth/{$action}");
    }

    /**
     * Gestion spécialisée pour les messages
     */
    public function handleMessages(Request $request, $action = null, $id = null)
    {
        $path = $action ? "messages/{$action}" : 'messages';
        if ($id) {
            $path .= "/{$id}";
        }
        return $this->handle($request, $path);
    }

    /**
     * Gestion spécialisée pour les incidents
     */
    public function handleIncidents(Request $request, $action = null, $id = null)
    {
        $path = $action ? "incidents/{$action}" : 'incidents';
        if ($id) {
            $path .= "/{$id}";
        }
        return $this->handle($request, $path);
    }

    /**
     * Gestion spécialisée pour les invités
     */
    public function handleGuests(Request $request, $action = null, $id = null)
    {
        $path = $action ? "guests/{$action}" : 'guests';
        if ($id) {
            $path .= "/{$id}";
        }
        return $this->handle($request, $path);
    }

    /**
     * Gestion spécialisée pour l'administration
     */
    public function handleAdmin(Request $request, $action = null, $subAction = null, $id = null)
    {
        $path = "admin";
        if ($action) {
            $path .= "/{$action}";
        }
        if ($subAction) {
            $path .= "/{$subAction}";
        }
        if ($id) {
            $path .= "/{$id}";
        }
        return $this->handle($request, $path);
    }

    /**
     * Gestion des fichiers et uploads
     */
    public function handleFiles(Request $request, $action, $id = null)
    {
        $path = "files/{$action}";
        if ($id) {
            $path .= "/{$id}";
        }
        return $this->handle($request, $path);
    }

    /**
     * Test de connectivité NHS
     */
    public function testConnection()
    {
        try {
            $response = Http::timeout(10)->get($this->nhsApiUrl . '/api/health');
            
            return response()->json([
                'success' => true,
                'nhs_status' => $response->successful() ? 'online' : 'offline',
                'nhs_response' => $response->json(),
                'proxy_status' => 'operational',
                'timestamp' => now()
            ]);
        } catch (\Exception $e) {
            return response()->json([
                'success' => false,
                'nhs_status' => 'offline',
                'error' => $e->getMessage(),
                'proxy_status' => 'degraded',
                'timestamp' => now()
            ], 503);
        }
    }
}
