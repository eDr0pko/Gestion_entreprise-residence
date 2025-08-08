<?php

namespace App\Http\Controllers;

use Illuminate\Http\Request;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\Log;
use App\Models\Personne;
use App\Models\GroupeMessage;
use App\Models\Visite;

/**
 * Contrôleur pour les accès directs à la base de données
 * Utilisé uniquement par backend-client pour des opérations SQL/ORM spécifiques
 * Ne contient AUCUNE logique métier - uniquement des requêtes DB
 */
class DatabaseController extends Controller
{
    /**
     * Vérification de l'état de la base de données
     */
    public function healthCheck()
    {
        try {
            $userCount = Personne::count();
            $groupCount = GroupeMessage::count();
            
            if ($userCount > 0) {
                $user = Personne::first();
                $conversations = GroupeMessage::whereHas('personnes', function ($query) use ($user) {
                    $query->where('personne_groupe.id_personne', $user->id_personne);
                })->count();
                
                return response()->json([
                    'status' => 'database_ok',
                    'users_count' => $userCount,
                    'groups_count' => $groupCount,
                    'sample_conversations' => $conversations,
                    'timestamp' => now()
                ]);
            }

            return response()->json([
                'status' => 'database_empty',
                'users_count' => $userCount,
                'groups_count' => $groupCount,
                'timestamp' => now()
            ]);
        } catch (\Exception $e) {
            return response()->json([
                'status' => 'database_error',
                'error' => $e->getMessage(),
                'timestamp' => now()
            ], 500);
        }
    }

    /**
     * Requête simple pour récupérer les visites (lecture seule)
     */
    public function getVisites(Request $request)
    {
        try {
            $user = Auth::user();
            if (!$user) {
                return response()->json(['success' => false, 'message' => 'Utilisateur non authentifié'], 401);
            }

            $role = $this->getUserRole($user);

            if ($role === 'admin' || $role === 'gardien') {
                $visites = DB::table('visite')
                    ->leftJoin('personne as invite', 'visite.id_invite', '=', 'invite.id_personne')
                    ->leftJoin('personne as visiteur', 'visite.email_visiteur', '=', 'visiteur.email')
                    ->select(
                        'visite.*',
                        'invite.nom as nom_invite',
                        'invite.prenom as prenom_invite',
                        'visiteur.nom as nom_visiteur',
                        'visiteur.prenom as prenom_visiteur'
                    )
                    ->orderBy('date_visite_start', 'desc')
                    ->get();
            } else {
                $visites = DB::table('visite')
                    ->leftJoin('personne as invite', 'visite.id_invite', '=', 'invite.id_personne')
                    ->leftJoin('personne as visiteur', 'visite.email_visiteur', '=', 'visiteur.email')
                    ->select(
                        'visite.*',
                        'invite.nom as nom_invite',
                        'invite.prenom as prenom_invite',
                        'visiteur.nom as nom_visiteur',
                        'visiteur.prenom as prenom_visiteur'
                    )
                    ->where(function($query) use ($user) {
                        $query->where('email_visiteur', $user->email)
                              ->orWhere('id_invite', $user->id_personne);
                    })
                    ->orderBy('date_visite_start', 'desc')
                    ->get();
            }

            return response()->json([
                'success' => true,
                'visites' => $visites
            ]);
        } catch (\Exception $e) {
            return response()->json([
                'success' => false,
                'message' => 'Erreur de base de données',
                'error' => $e->getMessage()
            ], 500);
        }
    }

    /**
     * Mise à jour simple du statut d'une visite (écriture contrôlée)
     */
    public function updateVisiteStatus(Request $request, $id)
    {
        try {
            $user = Auth::user();
            if (!$user) {
                return response()->json(['success' => false, 'message' => 'Utilisateur non authentifié'], 401);
            }

            $statut = $request->input('statut');
            $statutsAutorises = ['en_cours', 'annulee', 'terminee', 'programmee', 'banni'];

            if (!in_array($statut, $statutsAutorises)) {
                return response()->json(['success' => false, 'message' => 'Statut invalide'], 400);
            }

            // Vérifier les permissions selon le rôle
            $userRole = $this->getUserRole($user);
            
            $visite = DB::table('visite')
                ->where('id_visite', $id)
                ->first();

            if (!$visite) {
                return response()->json(['success' => false, 'message' => 'Visite non trouvée'], 404);
            }

            // Autoriser la modification selon le rôle
            $canModify = false;
            
            if ($userRole === 'admin' || $userRole === 'gardien') {
                // Admin et gardien peuvent modifier toutes les visites
                $canModify = true;
            } elseif ($userRole === 'resident') {
                // Un résident peut modifier les visites où il est l'invité
                $canModify = ($visite->id_invite == $user->id_personne);
            } elseif ($userRole === 'invite') {
                // Un invité peut modifier ses propres visites
                $canModify = ($visite->email_visiteur == $user->email);
            }

            if (!$canModify) {
                return response()->json(['success' => false, 'message' => 'Permission refusée'], 403);
            }

            DB::table('visite')
                ->where('id_visite', $id)
                ->update(['statut_visite' => $statut]);

            return response()->json(['success' => true, 'message' => 'Statut mis à jour']);
        } catch (\Exception $e) {
            return response()->json([
                'success' => false,
                'message' => 'Erreur de mise à jour',
                'error' => $e->getMessage()
            ], 500);
        }
    }
    
    /**
     * Détermine le rôle d'un utilisateur
     */
    private function getUserRole($user)
    {
        if (DB::table('admin')->where('id_personne', $user->id_personne)->exists()) {
            return 'admin';
        } elseif (DB::table('gardien')->where('id_personne', $user->id_personne)->exists()) {
            return 'gardien';
        } elseif (DB::table('resident')->where('id_personne', $user->id_personne)->exists()) {
            return 'resident';
        } elseif (DB::table('invite')->where('id_personne', $user->id_personne)->exists()) {
            return 'invite';
        }
        return null;
    }

    /**
     * Test de conversation pour vérification DB
     */
    public function testConversation()
    {
        try {
            $user = Personne::where('email', 'marie.durand@residence.com')->first();
            if (!$user) {
                return response()->json(['error' => 'User not found']);
            }

            $conversations = GroupeMessage::whereHas('personnes', function ($query) use ($user) {
                $query->where('personne_groupe.id_personne', $user->id_personne);
            })->get();

            return response()->json([
                'user_id' => $user->id_personne,
                'conversations_count' => $conversations->count(),
                'success' => true
            ]);
        } catch (\Exception $e) {
            return response()->json([
                'error' => $e->getMessage(),
                'line' => $e->getLine(),
                'file' => $e->getFile()
            ], 500);
        }
    }

    /**
     * Récupération des données utilisateur pour l'authentification locale
     */
    public function getUserData($id)
    {
        try {
            $user = Personne::find($id);
            if (!$user) {
                return response()->json(['success' => false, 'message' => 'Utilisateur non trouvé'], 404);
            }

            return response()->json([
                'success' => true,
                'user' => [
                    'id_personne' => $user->id_personne,
                    'email' => $user->email,
                    'nom' => $user->nom,
                    'prenom' => $user->prenom,
                    'numero_telephone' => $user->numero_telephone,
                    'photo_profil' => $user->photo_profil,
                ]
            ]);
        } catch (\Exception $e) {
            return response()->json([
                'success' => false,
                'message' => 'Erreur de récupération',
                'error' => $e->getMessage()
            ], 500);
        }
    }

    /**
     * Vérification de l'existence d'un utilisateur par email
     */
    public function checkUserExists($email)
    {
        try {
            $exists = Personne::where('email', $email)->exists();
            return response()->json([
                'success' => true,
                'exists' => $exists
            ]);
        } catch (\Exception $e) {
            return response()->json([
                'success' => false,
                'error' => $e->getMessage()
            ], 500);
        }
    }

    /**
     * Recherche de résidents (pour la création de visites)
     */
    public function searchResidents(Request $request)
    {
        try {
            $query = $request->get('q', '');
            
            if (strlen($query) < 2) {
                return response()->json(['residents' => []]);
            }

            $residents = DB::table('personne')
                ->join('resident', 'personne.id_personne', '=', 'resident.id_personne')
                ->where(function($q) use ($query) {
                    $q->where('personne.nom', 'like', "%$query%")
                      ->orWhere('personne.prenom', 'like', "%$query%")
                      ->orWhere('personne.email', 'like', "%$query%");
                })
                ->select(
                    'personne.id_personne', 
                    'personne.nom', 
                    'personne.prenom', 
                    'personne.email', 
                    'personne.numero_telephone'
                )
                ->limit(15)
                ->get();

            return response()->json(['residents' => $residents]);
        } catch (\Exception $e) {
            return response()->json([
                'success' => false,
                'error' => $e->getMessage()
            ], 500);
        }
    }

    /**
     * Recherche de visiteurs (personnes qui ne sont pas résidents)
     */
    public function searchVisitors(Request $request)
    {
        try {
            $query = $request->get('q', '');
            
            if (strlen($query) < 2) {
                return response()->json(['visitors' => []]);
            }

            // Récupérer les IDs des résidents pour les exclure
            $residentIds = DB::table('resident')->pluck('id_personne');

            $visitors = DB::table('personne')
                ->whereNotIn('id_personne', $residentIds)
                ->where(function($q) use ($query) {
                    $q->where('nom', 'like', "%$query%")
                      ->orWhere('prenom', 'like', "%$query%")
                      ->orWhere('email', 'like', "%$query%");
                })
                ->select(
                    'id_personne', 
                    'nom', 
                    'prenom', 
                    'email', 
                    'numero_telephone'
                )
                ->limit(15)
                ->get();

            return response()->json(['visitors' => $visitors]);
        } catch (\Exception $e) {
            return response()->json([
                'success' => false,
                'error' => $e->getMessage()
            ], 500);
        }
    }
}
