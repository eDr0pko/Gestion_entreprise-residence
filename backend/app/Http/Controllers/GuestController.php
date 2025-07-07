<?php

namespace App\Http\Controllers;

use Illuminate\Http\Request;
use Illuminate\Support\Facades\Validator;
use Illuminate\Support\Facades\Hash;
use Illuminate\Support\Facades\Log;
use App\Models\Personne;
use App\Models\Invite;

class GuestController extends Controller
{
    /**
     * Inscrire un nouvel invité avec mot de passe
     */
    public function register(Request $request)
    {
        try {
            Log::info('Tentative d\'inscription invité:', $request->all());

            // Validation des données
            $validator = Validator::make($request->all(), [
                'email' => 'required|email|unique:personne,email',
                'nom' => 'required|string|min:2|max:45',
                'prenom' => 'required|string|min:2|max:45',
                'numero_telephone' => 'required|string',
                'mot_de_passe' => 'required|string|min:6',
                'mot_de_passe_confirmation' => 'required|string|same:mot_de_passe'
            ], [
                'email.required' => 'L\'adresse email est obligatoire',
                'email.email' => 'L\'adresse email n\'est pas valide',
                'email.unique' => 'Cette adresse email est déjà utilisée',
                'nom.required' => 'Le nom est obligatoire',
                'nom.min' => 'Le nom doit contenir au moins 2 caractères',
                'nom.max' => 'Le nom ne peut pas dépasser 45 caractères',
                'prenom.required' => 'Le prénom est obligatoire',
                'prenom.min' => 'Le prénom doit contenir au moins 2 caractères',
                'prenom.max' => 'Le prénom ne peut pas dépasser 45 caractères',
                'numero_telephone.required' => 'Le numéro de téléphone est obligatoire',
                'mot_de_passe.required' => 'Le mot de passe est obligatoire',
                'mot_de_passe.min' => 'Le mot de passe doit contenir au moins 6 caractères',
                'mot_de_passe_confirmation.required' => 'La confirmation du mot de passe est obligatoire',
                'mot_de_passe_confirmation.same' => 'Les mots de passe ne correspondent pas'
            ]);

            if ($validator->fails()) {
                return response()->json([
                    'success' => false,
                    'message' => 'Données invalides',
                    'errors' => $validator->errors()
                ], 422);
            }

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

            // Créer la personne avec mot de passe hashé
            $personne = Personne::create([
                'email' => strtolower(trim($request->email)),
                'nom' => trim($request->nom),
                'prenom' => trim($request->prenom),
                'numero_telephone' => $cleanPhone, // Utiliser le numéro nettoyé
                'mot_de_passe' => Hash::make($request->mot_de_passe)
            ]);

            // Créer l'entrée invite (actif par défaut)
            $invite = Invite::create([
                'id_personne' => $personne->id_personne,
                'actif' => true
            ]);

            // Générer un token pour l'authentification
            $token = $personne->createToken('guest_auth_token')->plainTextToken;

            Log::info('Inscription invité réussie:', ['email' => $personne->email]);

            return response()->json([
                'success' => true,
                'message' => 'Inscription réussie ! Vous êtes maintenant connecté.',
                'token' => $token,
                'user' => [
                    'id_personne' => $personne->id_personne,
                    'email' => $personne->email,
                    'nom' => $personne->nom,
                    'prenom' => $personne->prenom,
                    'numero_telephone' => $personne->numero_telephone,
                    'photo_profil' => $personne->photo_profil,
                    'role' => 'invite'
                ]
            ]);

        } catch (\Exception $e) {
            Log::error('Erreur lors de l\'inscription invité:', ['error' => $e->getMessage()]);
            return response()->json([
                'success' => false,
                'message' => 'Erreur lors de l\'inscription: ' . $e->getMessage()
            ], 500);
        }
    }

    /**
     * Connexion d'un invité avec email et mot de passe
     */
    public function login(Request $request)
    {
        try {
            Log::info('Tentative de connexion invité:', ['email' => $request->email]);

            // Validation
            $validator = Validator::make($request->all(), [
                'email' => 'required|email',
                'mot_de_passe' => 'required|string'
            ], [
                'email.required' => 'L\'adresse email est obligatoire',
                'email.email' => 'L\'adresse email n\'est pas valide',
                'mot_de_passe.required' => 'Le mot de passe est obligatoire'
            ]);

            if ($validator->fails()) {
                return response()->json([
                    'success' => false,
                    'message' => 'Données invalides',
                    'errors' => $validator->errors()
                ], 422);
            }

            // Vérifier que la personne existe et est un invité
            $personne = Personne::with('invite')->where('email', $request->email)->first();

            if (!$personne || !$personne->invite) {
                return response()->json([
                    'success' => false,
                    'message' => 'Aucun compte invité trouvé avec cette adresse email'
                ], 404);
            }

            // Vérifier que l'invité est actif
            if (!$personne->invite->actif) {
                return response()->json([
                    'success' => false,
                    'message' => 'Votre compte invité a été désactivé. Contactez l\'administration.'
                ], 403);
            }

            // Vérifier le mot de passe
            if (!Hash::check($request->mot_de_passe, $personne->mot_de_passe)) {
                Log::warning('Mot de passe incorrect pour invité:', ['email' => $request->email]);
                return response()->json([
                    'success' => false,
                    'message' => 'Mot de passe incorrect'
                ], 422);
            }

            // Supprimer les anciens tokens
            $personne->tokens()->delete();

            // Générer un nouveau token
            $token = $personne->createToken('guest_auth_token')->plainTextToken;

            Log::info('Connexion invité réussie:', ['email' => $personne->email]);

            return response()->json([
                'success' => true,
                'message' => 'Connexion réussie !',
                'token' => $token,
                'user' => [
                    'id_personne' => $personne->id_personne,
                    'email' => $personne->email,
                    'nom' => $personne->nom,
                    'prenom' => $personne->prenom,
                    'numero_telephone' => $personne->numero_telephone,
                    'photo_profil' => $personne->photo_profil,
                    'role' => 'invite'
                ]
            ]);

        } catch (\Exception $e) {
            Log::error('Erreur lors de la connexion invité:', ['error' => $e->getMessage()]);
            return response()->json([
                'success' => false,
                'message' => 'Erreur lors de la connexion: ' . $e->getMessage()
            ], 500);
        }
    }

    /**
     * Liste des invités (pour admin)
     */
    public function index()
    {
        try {
            $invites = Personne::with('invite')
                ->whereHas('invite')
                ->get()
                ->map(function ($personne) {
                    return [
                        'id_personne' => $personne->id_personne,
                        'email' => $personne->email,
                        'nom' => $personne->nom,
                        'prenom' => $personne->prenom,
                        'numero_telephone' => $personne->numero_telephone,
                        'photo_profil' => $personne->photo_profil,
                        'invite' => [
                            'actif' => $personne->invite->actif,
                            'created_at' => $personne->invite->created_at,
                            'updated_at' => $personne->invite->updated_at
                        ]
                    ];
                });

            return response()->json([
                'success' => true,
                'data' => $invites
            ]);

        } catch (\Exception $e) {
            return response()->json([
                'success' => false,
                'message' => 'Erreur: ' . $e->getMessage()
            ], 500);
        }
    }

    /**
     * Désactiver un invité (pour admin)
     */
    public function deactivate($email)
    {
        try {
            // Trouver la personne par email puis l'invité par id_personne
            $personne = Personne::where('email', $email)->first();
            
            if (!$personne) {
                return response()->json([
                    'success' => false,
                    'message' => 'Personne non trouvée'
                ], 404);
            }

            $invite = Invite::where('id_personne', $personne->id_personne)->first();

            if (!$invite) {
                return response()->json([
                    'success' => false,
                    'message' => 'Invité non trouvé'
                ], 404);
            }

            $invite->update(['actif' => false]);

            return response()->json([
                'success' => true,
                'message' => 'Invité désactivé avec succès'
            ]);

        } catch (\Exception $e) {
            return response()->json([
                'success' => false,
                'message' => 'Erreur: ' . $e->getMessage()
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
