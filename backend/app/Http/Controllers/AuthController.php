<?php

namespace App\Http\Controllers;

use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\Hash;
use Illuminate\Support\Facades\Log;
use App\Models\Personne;

class AuthController extends Controller
{
    /**
     * Connexion de l'utilisateur
     */
    public function login(Request $request)
    {
        try {
            Log::info('Tentative de connexion pour: ' . $request->email);
            
            $request->validate([
                'email' => 'required|email',
                'password' => 'required|string',
            ]);

            // Trouver l'utilisateur
            $user = Personne::where('email', $request->email)->first();

            if (!$user) {
                Log::warning('Utilisateur non trouvé: ' . $request->email);
                return response()->json([
                    'success' => false,
                    'message' => 'Utilisateur non trouvé'
                ], 404);
            }

            if (!Hash::check($request->password, $user->mot_de_passe)) {
                Log::warning('Mot de passe incorrect pour: ' . $request->email);
                return response()->json([
                    'success' => false,
                    'message' => 'Mot de passe incorrect'
                ], 422);
            }

            // Supprimer les anciens tokens
            $user->tokens()->delete();

            // Créer le token
            $token = $user->createToken('auth_token')->plainTextToken;
            
            Log::info('Token créé pour: ' . $user->email);

            // Charger les relations pour déterminer le rôle
            $user->load(['admin', 'gardien', 'resident']);

            return response()->json([
                'success' => true,
                'access_token' => $token,
                'token_type' => 'Bearer',
                'user' => [
                    'email' => $user->email,
                    'nom' => $user->nom,
                    'prenom' => $user->prenom,
                    'nom_complet' => $user->nom_complet,
                    'numero_telephone' => $user->numero_telephone,
                    'role' => $user->getRole(),
                ]
            ]);
        } catch (\Exception $e) {
            Log::error('Erreur de connexion: ' . $e->getMessage());
            return response()->json([
                'success' => false,
                'message' => 'Erreur lors de la connexion',
                'error' => $e->getMessage()
            ], 500);
        }
    }

    /**
     * Déconnexion de l'utilisateur
     */
    public function logout(Request $request)
    {
        try {
            $request->user()->currentAccessToken()->delete();

            return response()->json([
                'success' => true,
                'message' => 'Déconnexion réussie'
            ]);
        } catch (\Exception $e) {
            return response()->json([
                'success' => false,
                'message' => 'Erreur lors de la déconnexion',
                'error' => $e->getMessage()
            ], 500);
        }
    }

    /**
     * Obtenir les informations de l'utilisateur connecté
     */
    public function me(Request $request)
    {
        try {
            $user = $request->user();
            $user->load(['admin', 'gardien', 'resident']);

            return response()->json([
                'success' => true,
                'user' => [
                    'email' => $user->email,
                    'nom' => $user->nom,
                    'prenom' => $user->prenom,
                    'nom_complet' => $user->nom_complet,
                    'numero_telephone' => $user->numero_telephone,
                    'role' => $user->getRole(),
                ]
            ]);
        } catch (\Exception $e) {
            return response()->json([
                'success' => false,
                'message' => 'Erreur lors de la récupération des informations utilisateur',
                'error' => $e->getMessage()
            ], 500);
        }
    }

    /**
     * Vérifier si l'utilisateur est authentifié
     */
    public function check(Request $request)
    {
        try {
            $user = $request->user();
            
            if (!$user) {
                return response()->json([
                    'success' => false,
                    'message' => 'Non authentifié'
                ], 401);
            }
            
            Log::info('Vérification auth pour: ' . $user->email);
            
            return response()->json([
                'success' => true,
                'authenticated' => true,
                'user' => [
                    'email' => $user->email,
                    'nom' => $user->nom,
                    'prenom' => $user->prenom,
                ]
            ]);
        } catch (\Exception $e) {
            Log::error('Erreur vérification auth: ' . $e->getMessage());
            return response()->json([
                'success' => false,
                'message' => 'Erreur de vérification'
            ], 500);
        }
    }

    // ...autres méthodes...
}
