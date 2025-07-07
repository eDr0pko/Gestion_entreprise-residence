<?php

namespace App\Http\Controllers;

use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\Hash;
use Illuminate\Support\Facades\Log;
use App\Models\Personne;
use Illuminate\Support\Facades\DB;

class AuthController extends Controller
{
    /**
     * Récupérer l'utilisateur actuel (membre ou invité)
     */
    private function getCurrentUser(Request $request)
    {
        // Avec Sanctum, l'utilisateur est disponible directement
        $user = $request->user();
        
        if (!$user) {
            \Log::info('No authenticated user found in AuthController');
            return null;
        }
        
        \Log::info('Authenticated user found in AuthController:', [
            'email' => $user->email,
            'class' => get_class($user)
        ]);
        
        return $user;
    }
    /**
     * Connexion de l'utilisateur
     */
    public function login(Request $request)
    {
        try {
            Log::info('Tentative de connexion pour: ' . $request->email);
            Log::info('Request data received: ' . json_encode($request->all()));
            
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
                    'id_personne' => $user->id_personne,
                    'email' => $user->email,
                    'nom' => $user->nom,
                    'prenom' => $user->prenom,
                    'nom_complet' => $user->nom_complet,
                    'numero_telephone' => $user->numero_telephone,
                    'photo_profil' => $user->photo_profil,
                    'role' => $user->getRole(),
                ]
            ]);
        } catch (\Illuminate\Validation\ValidationException $e) {
            Log::error('Erreur de validation: ' . json_encode($e->errors()));
            return response()->json([
                'success' => false,
                'message' => 'Données de validation incorrectes',
                'errors' => $e->errors()
            ], 422);
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

    /**
     * Obtenir les statistiques du profil utilisateur
     */
    public function getProfileStats(Request $request)
    {
        try {
            $user = $this->getCurrentUser($request);
            
            if (!$user) {
                return response()->json([
                    'success' => false,
                    'message' => 'Utilisateur non authentifié'
                ], 401);
            }

            // Compter les messages envoyés par l'utilisateur
            $messagesEnvoyes = DB::table('message')
                ->where('id_auteur', $user->id_personne)
                ->count();

            // Compter les réactions données par l'utilisateur
            $reactionsData = DB::table('message_reaction')
                ->where('id_personne', $user->id_personne)
                ->count();

            // Compter le nombre de groupes auxquels l'utilisateur participe
            $groupesParticipes = DB::table('personne_groupe')
                ->where('id_personne', $user->id_personne)
                ->count();

            // Obtenir la date de dernière connexion
            $derniereConnexion = DB::table('personne_groupe')
                ->where('id_personne', $user->id_personne)
                ->orderBy('derniere_connexion', 'desc')
                ->value('derniere_connexion');

            // Compter les messages non lus basé sur la dernière connexion
            $messagesNonLus = DB::table('message')
                ->join('personne_groupe', 'message.id_groupe_message', '=', 'personne_groupe.id_groupe_message')
                ->where('personne_groupe.id_personne', $user->id_personne)
                ->where('message.id_auteur', '!=', $user->id_personne) // Exclure ses propres messages
                ->where(function($query) {
                    // Messages envoyés après la dernière connexion OU jamais connecté
                    $query->whereColumn('message.date_envoi', '>', 'personne_groupe.derniere_connexion')
                          ->orWhereNull('personne_groupe.derniere_connexion');
                })
                ->count();

            // Obtenir le rôle de l'utilisateur
            $role = 'Résident';
            if (DB::table('admin')->where('id_personne', $user->id_personne)->exists()) {
                $role = 'Administrateur';
            } elseif (DB::table('gardien')->where('id_personne', $user->id_personne)->exists()) {
                $role = 'Gardien';
            } elseif (DB::table('invite')->where('id_personne', $user->id_personne)->exists()) {
                $role = 'Invité';
            }

            // Date d'inscription (estimation basée sur la première participation à un groupe)
            $dateInscription = DB::table('personne_groupe')
                ->where('id_personne', $user->id_personne)
                ->orderBy('date_adhesion', 'asc')
                ->value('date_adhesion');

            return response()->json([
                'success' => true,
                'data' => [
                    'messages_envoyes' => $messagesEnvoyes,
                    'reactions_donnees' => $reactionsData,
                    'groupes_participes' => $groupesParticipes,
                    'messages_non_lus' => $messagesNonLus,
                    'derniere_connexion' => $derniereConnexion,
                    'date_inscription' => $dateInscription,
                    'role' => $role
                ]
            ]);

        } catch (\Exception $e) {
            Log::error('Erreur lors de la récupération des statistiques: ' . $e->getMessage());
            
            return response()->json([
                'success' => false,
                'message' => 'Erreur lors de la récupération des statistiques',
                'error' => $e->getMessage()
            ], 500);
        }
    }

    /**
     * Mettre à jour les informations du profil
     */
    public function updateProfile(Request $request)
    {
        try {
            $user = $this->getCurrentUser($request);
            
            if (!$user) {
                return response()->json([
                    'success' => false,
                    'message' => 'Utilisateur non authentifié'
                ], 401);
            }

            $request->validate([
                'nom' => 'required|string|max:45',
                'prenom' => 'required|string|max:45',
                'numero_telephone' => 'required|string'
            ]);

            // Valider et nettoyer le numéro de téléphone
            $cleanPhone = $this->cleanAndValidatePhone($request->numero_telephone);
            if (!$cleanPhone) {
                return response()->json([
                    'success' => false,
                    'message' => 'Format de téléphone invalide',
                    'errors' => [
                        'numero_telephone' => ['Le numéro de téléphone doit être au format international (+33123456789)']
                    ]
                ], 422);
            }

            DB::table('personne')
                ->where('id_personne', $user->id_personne)
                ->update([
                    'nom' => $request->nom,
                    'prenom' => $request->prenom,
                    'numero_telephone' => $cleanPhone // Utiliser le numéro nettoyé
                ]);

            return response()->json([
                'success' => true,
                'message' => 'Profil mis à jour avec succès',
                'data' => [
                    'email' => $user->email,
                    'nom' => $request->nom,
                    'prenom' => $request->prenom,
                    'numero_telephone' => $cleanPhone // Retourner le numéro nettoyé
                ]
            ]);

        } catch (\Illuminate\Validation\ValidationException $e) {
            return response()->json([
                'success' => false,
                'message' => 'Données invalides',
                'errors' => $e->errors()
            ], 422);
        } catch (\Exception $e) {
            Log::error('Erreur lors de la mise à jour du profil: ' . $e->getMessage());
            
            return response()->json([
                'success' => false,
                'message' => 'Erreur lors de la mise à jour du profil',
                'error' => $e->getMessage()
            ], 500);
        }
    }

    /**
     * Vérifier le mot de passe actuel
     */
    public function verifyPassword(Request $request)
    {
        try {
            $user = $this->getCurrentUser($request);
            
            if (!$user) {
                return response()->json([
                    'success' => false,
                    'message' => 'Utilisateur non authentifié'
                ], 401);
            }

            $request->validate([
                'current_password' => 'required'
            ]);

            // Vérifier le mot de passe actuel
            if (!Hash::check($request->current_password, $user->mot_de_passe)) {
                return response()->json([
                    'success' => false,
                    'message' => 'Mot de passe actuel incorrect'
                ], 422);
            }

            return response()->json([
                'success' => true,
                'message' => 'Mot de passe vérifié avec succès'
            ]);

        } catch (\Illuminate\Validation\ValidationException $e) {
            return response()->json([
                'success' => false,
                'message' => 'Données invalides',
                'errors' => $e->errors()
            ], 422);
        } catch (\Exception $e) {
            Log::error('Erreur lors de la vérification du mot de passe: ' . $e->getMessage());
            
            return response()->json([
                'success' => false,
                'message' => 'Erreur lors de la vérification du mot de passe',
                'error' => $e->getMessage()
            ], 500);
        }
    }

    /**
     * Changer le mot de passe
     */
    public function updatePassword(Request $request)
    {
        try {
            $user = $this->getCurrentUser($request);
            
            if (!$user) {
                return response()->json([
                    'success' => false,
                    'message' => 'Utilisateur non authentifié'
                ], 401);
            }

            $request->validate([
                'current_password' => 'required',
                'new_password' => 'required|min:8|confirmed'
            ]);

            // Vérifier le mot de passe actuel
            if (!Hash::check($request->current_password, $user->mot_de_passe)) {
                return response()->json([
                    'success' => false,
                    'message' => 'Mot de passe actuel incorrect'
                ], 422);
            }

            // Mettre à jour le mot de passe
            DB::table('personne')
                ->where('id_personne', $user->id_personne)
                ->update([
                    'mot_de_passe' => Hash::make($request->new_password)
                ]);

            return response()->json([
                'success' => true,
                'message' => 'Mot de passe mis à jour avec succès'
            ]);

        } catch (\Illuminate\Validation\ValidationException $e) {
            return response()->json([
                'success' => false,
                'message' => 'Données invalides',
                'errors' => $e->errors()
            ], 422);
        } catch (\Exception $e) {
            Log::error('Erreur lors du changement de mot de passe: ' . $e->getMessage());
            
            return response()->json([
                'success' => false,
                'message' => 'Erreur lors du changement de mot de passe',
                'error' => $e->getMessage()
            ], 500);
        }
    }

    /**
     * Upload d'avatar (placeholder pour future implémentation)
     */
    public function uploadAvatar(Request $request)
    {
        try {
            $user = $this->getCurrentUser($request);
            
            if (!$user) {
                return response()->json([
                    'success' => false,
                    'message' => 'Utilisateur non authentifié'
                ], 401);
            }

            // Validation de l'image
            $request->validate([
                'avatar' => 'required|image|mimes:jpeg,png,jpg,gif|max:2048' // 2MB max
            ]);

            // Supprimer l'ancienne photo de profil s'il y en a une
            if ($user->photo_profil) {
                $oldPath = storage_path('app/public/' . $user->photo_profil);
                if (file_exists($oldPath)) {
                    unlink($oldPath);
                }
            }

            // Stocker la nouvelle image
            $file = $request->file('avatar');
            $fileName = 'avatar_' . $user->id_personne . '_' . time() . '.' . $file->getClientOriginalExtension();
            $filePath = $file->storeAs('avatars', $fileName, 'public');

            // Mettre à jour l'utilisateur
            $user->photo_profil = $filePath;
            $user->save();

            // Générer l'URL complète de l'avatar
            $avatarUrl = url('/storage/' . $filePath);

            return response()->json([
                'success' => true,
                'message' => 'Photo de profil mise à jour avec succès',
                'data' => [
                    'photo_profil' => $filePath,
                    'avatar_url' => $avatarUrl
                ]
            ]);

        } catch (\Illuminate\Validation\ValidationException $e) {
            return response()->json([
                'success' => false,
                'message' => 'Fichier invalide',
                'errors' => $e->errors()
            ], 422);
        } catch (\Exception $e) {
            Log::error('Erreur lors de l\'upload d\'avatar: ' . $e->getMessage());
            
            return response()->json([
                'success' => false,
                'message' => 'Erreur lors de l\'upload d\'avatar',
                'error' => $e->getMessage()
            ], 500);
        }
    }

    public function deleteAvatar(Request $request)
    {
        try {
            $user = $this->getCurrentUser($request);
            
            if (!$user) {
                return response()->json([
                    'success' => false,
                    'message' => 'Utilisateur non authentifié'
                ], 401);
            }

            // Supprimer le fichier s'il existe
            if ($user->photo_profil) {
                $filePath = storage_path('app/public/' . $user->photo_profil);
                if (file_exists($filePath)) {
                    unlink($filePath);
                }

                // Mettre à jour l'utilisateur
                $user->photo_profil = null;
                $user->save();
            }

            return response()->json([
                'success' => true,
                'message' => 'Photo de profil supprimée avec succès'
            ]);

        } catch (\Exception $e) {
            Log::error('Erreur lors de la suppression d\'avatar: ' . $e->getMessage());
            
            return response()->json([
                'success' => false,
                'message' => 'Erreur lors de la suppression d\'avatar',
                'error' => $e->getMessage()
            ], 500);
        }
    }

    public function getAvatar(Request $request, $filename)
    {
        try {
            $user = $this->getCurrentUser($request);
            
            if (!$user) {
                return response()->json([
                    'success' => false,
                    'message' => 'Utilisateur non authentifié'
                ], 401);
            }

            $avatarPath = storage_path('app/public/avatars/' . $filename);
            
            if (!file_exists($avatarPath)) {
                return response()->json([
                    'success' => false,
                    'message' => 'Avatar non trouvé'
                ], 404);
            }

            // Obtenir le type MIME du fichier
            $mimeType = mime_content_type($avatarPath);
            
            return response()->file($avatarPath, [
                'Content-Type' => $mimeType,
                'Cache-Control' => 'public, max-age=31536000', // Cache d'un an
            ]);

        } catch (\Exception $e) {
            Log::error('Erreur lors de la récupération d\'avatar: ' . $e->getMessage());
            
            return response()->json([
                'success' => false,
                'message' => 'Erreur lors de la récupération d\'avatar',
                'error' => $e->getMessage()
            ], 500);
        }
    }

    /**
     * Nettoie un numéro de téléphone en supprimant les espaces
     * et vérifie le format international
     */
    private function cleanAndValidatePhone($phone)
    {
        // Supprimer tous les espaces
        $cleanPhone = preg_replace('/\s+/', '', $phone);
        
        // Vérifier le format international
        if (!preg_match('/^\+\d{1,4}\d{6,15}$/', $cleanPhone)) {
            return null;
        }
        
        return $cleanPhone;
    }
}
