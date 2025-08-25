<?php
namespace App\Http\Controllers;

use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\Hash;
use Illuminate\Support\Facades\Log;
use App\Models\Personne;
use Illuminate\Support\Facades\DB;
use App\Traits\LoggableAction;

class AuthController extends Controller
{
    use LoggableAction;
        /**
         * Récupérer tous les administrateurs, gardiens et résidents (admin)
         */
        public function createPerson(Request $request)
        {
            try {
                $user = $request->user();
                if (!$user || !$user->admin) {
                    $this->logAction($user ? $user->id_personne : null, 'unauthorized_create_person', "Tentative non autorisée de création d'utilisateur", $request);
                    return response()->json(['success' => false, 'message' => 'Non autorisé'], 403);
                }
                
                \Log::info('Tentative de création d\'utilisateur', ['données' => $request->all()]);
                
                $roles = $request->input('roles', []);
                if (!is_array($roles) || count($roles) === 0) {
                    $this->logAction($user ? $user->id_personne : null, 'invalid_create_person', "Tentative de création d'utilisateur sans rôle.", $request);
                    return response()->json(['success' => false, 'message' => 'Au moins un rôle doit être sélectionné.'], 422);
                }
                $data = $request->only(['nom', 'prenom', 'numero_telephone', 'email']);
                if (!$request->filled('password')) {
                    $this->logAction($user ? $user->id_personne : null, 'invalid_create_person', "Tentative de création d'utilisateur sans mot de passe.", $request);
                    return response()->json(['success' => false, 'message' => 'Mot de passe requis.'], 422);
                }
                $data['mot_de_passe'] = \Hash::make($request->input('password'));
                
                \Log::info('Données à insérer dans personne', ['data' => $data]);
                
                // Vérifier unicité email
                if (\App\Models\Personne::where('email', $data['email'])->exists()) {
                    $this->logAction($user ? $user->id_personne : null, 'duplicate_email_create_person', "Tentative de création d'utilisateur avec email déjà utilisé: " . $data['email'], $request);
                    return response()->json(['success' => false, 'message' => 'Email déjà utilisé.'], 422);
                }
                
                $personne = \App\Models\Personne::create($data);
                \Log::info('Personne créée avec succès', ['id' => $personne->id_personne]);
                
                // ADMIN
                if (in_array('admin', $roles)) {
                    \Log::info('Création du rôle admin');
                    \App\Models\Admin::create(['id_personne' => $personne->id_personne, 'niveau_acces' => 'admin_standard', 'date_nomination' => now()]);
                }
                // GARDIEN
                if (in_array('gardien', $roles)) {
                    \Log::info('Création du rôle gardien');
                    \App\Models\Gardien::create(['id_personne' => $personne->id_personne]);
                }
                // RESIDENT
                if (in_array('resident', $roles)) {
                    \Log::info('Création du rôle resident', ['adresse' => $request->input('adresse_logement', null)]);
                    \App\Models\Resident::create([
                        'id_personne' => $personne->id_personne,
                        'adresse_logement' => $request->input('adresse_logement', null)
                    ]);
                }
                // Log creation
                $this->logAction($user ? $user->id_personne : null, 'create_person', 'Création d\'un nouvel utilisateur: ' . $data['email'], $request);
                return response()->json(['success' => true, 'personne' => $personne->fresh()]);
            } catch (\Exception $e) {
                \Log::error('Erreur lors de la création d\'utilisateur:', [
                    'message' => $e->getMessage(),
                    'file' => $e->getFile(),
                    'line' => $e->getLine(),
                    'trace' => $e->getTraceAsString()
                ]);
                return response()->json(['success' => false, 'message' => 'Erreur lors de la création: ' . $e->getMessage()], 500);
            }
        }
        
        public function getAllPersons(Request $request)
        {
            $user = $request->user();
            if (!$user || !$user->admin) {
                $this->logAction($user ? $user->id_personne : null, 'unauthorized_update_person', "Tentative non autorisée de modification d'utilisateur", $request);
                return response()->json(['success' => false, 'message' => 'Non autorisé'], 403);
            }

            // Récupérer tous les rôles et fusionner par personne
            $personnes = collect();
            // Admins
            foreach (\App\Models\Admin::with('personne')->get() as $a) {
                if ($a->personne) {
                    $id = $a->personne->id_personne;
                    $personnes[$id] = array_merge($personnes[$id] ?? [
                        'id' => $id,
                        'email' => $a->personne->email,
                        'nom' => $a->personne->nom,
                        'prenom' => $a->personne->prenom,
                        'numero_telephone' => $a->personne->numero_telephone,
                        'photo_profil' => $a->personne->photo_profil,
                        'roles' => [],
                    ], [
                        'roles' => array_unique(array_merge(($personnes[$id]['roles'] ?? []), ['admin'])),
                        'niveau_acces' => $a->niveau_acces,
                        'date_nomination' => $a->date_nomination
                    ]);
                }
            }
            // Gardiens
            foreach (\App\Models\Gardien::with('personne')->get() as $g) {
                if ($g->personne) {
                    $id = $g->personne->id_personne;
                    $personnes[$id] = array_merge($personnes[$id] ?? [
                        'id' => $id,
                        'email' => $g->personne->email,
                        'nom' => $g->personne->nom,
                        'prenom' => $g->personne->prenom,
                        'numero_telephone' => $g->personne->numero_telephone,
                        'photo_profil' => $g->personne->photo_profil,
                        'roles' => [],
                    ], [
                        'roles' => array_unique(array_merge(($personnes[$id]['roles'] ?? []), ['gardien'])),
                    ]);
                }
            }
            // Residents
            foreach (\App\Models\Resident::with('personne')->get() as $r) {
                if ($r->personne) {
                    $id = $r->personne->id_personne;
                    $personnes[$id] = array_merge($personnes[$id] ?? [
                        'id' => $id,
                        'email' => $r->personne->email,
                        'nom' => $r->personne->nom,
                        'prenom' => $r->personne->prenom,
                        'numero_telephone' => $r->personne->numero_telephone,
                        'photo_profil' => $r->personne->photo_profil,
                        'roles' => [],
                    ], [
                        'roles' => array_unique(array_merge(($personnes[$id]['roles'] ?? []), ['resident'])),
                        'adresse_logement' => $r->adresse_logement
                    ]);
                }
            }
            // Retourne la liste fusionnée
            return response()->json([
                'success' => true,
                'persons' => array_values($personnes->toArray())
            ]);
        }

        /**
         * Modifier un utilisateur (admin)
         */
        public function updatePerson(Request $request, $id)
        {
            $user = $request->user();
            if (!$user || !$user->admin) {
                return response()->json(['success' => false, 'message' => 'Non autorisé'], 403);
            }
            $personne = \App\Models\Personne::find($id);
            if (!$personne) {
                $this->logAction($user ? $user->id_personne : null, 'update_person_not_found', "Tentative de modification d'utilisateur inexistant (id: $id)", $request);
                return response()->json(['success' => false, 'message' => 'Utilisateur non trouvé'], 404);
            }
            $data = $request->only(['nom', 'prenom', 'numero_telephone', 'email']);
            $roles = $request->input('roles', []);
            if (!is_array($roles) || count($roles) === 0) {
                $this->logAction($user ? $user->id_personne : null, 'invalid_update_person', "Tentative de modification d'utilisateur sans rôle.", $request);
                return response()->json(['success' => false, 'message' => 'Au moins un rôle doit être sélectionné.'], 422);
            }

            // Update main person fields
            $personne->update($data);
            $this->logAction($user ? $user->id_personne : null, 'update_person', 'Modification de l\'utilisateur: ' . $personne->email, $request);

            // --- ADMIN ---
            $isAdmin = in_array('admin', $roles);
            $admin = \App\Models\Admin::where('id_personne', $personne->id_personne)->first();
            if ($isAdmin && !$admin) {
                \App\Models\Admin::create(['id_personne' => $personne->id_personne, 'niveau_acces' => 1, 'date_nomination' => now()]);
                $this->logAction($user ? $user->id_personne : null, 'add_admin_role', 'Ajout du rôle admin à: ' . $personne->email, $request);
            } elseif (!$isAdmin && $admin) {
                $admin->delete();
                $this->logAction($user ? $user->id_personne : null, 'remove_admin_role', 'Suppression du rôle admin de: ' . $personne->email, $request);
            }

            // --- GARDIEN ---
            $isGardien = in_array('gardien', $roles);
            $gardien = \App\Models\Gardien::where('id_personne', $personne->id_personne)->first();
            if ($isGardien && !$gardien) {
                \App\Models\Gardien::create(['id_personne' => $personne->id_personne]);
                $this->logAction($user ? $user->id_personne : null, 'add_gardien_role', 'Ajout du rôle gardien à: ' . $personne->email, $request);
            } elseif (!$isGardien && $gardien) {
                $gardien->delete();
                $this->logAction($user ? $user->id_personne : null, 'remove_gardien_role', 'Suppression du rôle gardien de: ' . $personne->email, $request);
            }

            // --- RESIDENT ---
            $isResident = in_array('resident', $roles);
            $resident = \App\Models\Resident::where('id_personne', $personne->id_personne)->first();
            if ($isResident && !$resident) {
                \App\Models\Resident::create([
                    'id_personne' => $personne->id_personne,
                    'adresse_logement' => $request->input('adresse_logement', null)
                ]);
                $this->logAction($user ? $user->id_personne : null, 'add_resident_role', 'Ajout du rôle résident à: ' . $personne->email, $request);
            } elseif (!$isResident && $resident) {
                $resident->delete();
                $this->logAction($user ? $user->id_personne : null, 'remove_resident_role', 'Suppression du rôle résident de: ' . $personne->email, $request);
            } elseif ($isResident && $resident) {
                // Update address if changed
                $resident->adresse_logement = $request->input('adresse_logement', $resident->adresse_logement);
                $resident->save();
                $this->logAction($user ? $user->id_personne : null, 'update_resident_address', 'Modification de l\'adresse résident: ' . $personne->email, $request);
            }

            // --- PASSWORD ---
            if ($request->filled('new_password')) {
                $personne->mot_de_passe = \Hash::make($request->input('new_password'));
                $personne->save();
            }

            return response()->json(['success' => true, 'personne' => $personne->fresh()]);
        }

        /**
         * Supprimer un utilisateur (admin)
         */
        public function deletePerson(Request $request, $id)
        {
            $user = $request->user();
            if (!$user || !$user->admin) {
                $this->logAction($user ? $user->id_personne : null, 'unauthorized_delete_person', "Tentative non autorisée de suppression d'utilisateur", $request);
                return response()->json(['success' => false, 'message' => 'Non autorisé'], 403);
            }
            $personne = \App\Models\Personne::find($id);
            if (!$personne) {
                $this->logAction($user ? $user->id_personne : null, 'delete_person_not_found', "Tentative de suppression d'utilisateur inexistant (id: $id)", $request);
                return response()->json(['success' => false, 'message' => 'Utilisateur non trouvé'], 404);
            }

            // Supprimer la photo de profil si elle existe
            if ($personne->photo_profil) {
                $avatarDir = storage_path('app/public/avatars/');
                // Suppression de tous les fichiers qui commencent par l'id de la personne ou qui contiennent son nom
                foreach (glob($avatarDir . '*') as $file) {
                    if (strpos(basename($file), (string)$personne->id_personne) !== false || basename($file) === $personne->photo_profil) {
                        @unlink($file);
                    }
                }
            }

            // Récupérer tous les groupes où la personne est membre
            $groupes = $personne->groupes()->get();
            foreach ($groupes as $groupe) {
                // Retirer la personne du groupe (pivot)
                $groupe->personnes()->detach($personne->id_personne);

                // Recompter les membres restants
                $membresRestants = $groupe->personnes()->count();
                if ($membresRestants < 2) {
                    // Supprimer tous les messages de ce groupe
                    foreach ($groupe->messages as $message) {
                        // Supprimer les fichiers liés à ce message
                        foreach ($message->fichiers as $fichier) {
                            $filePath = storage_path('app/public/messages/' . $fichier->chemin_fichier);
                            if (file_exists($filePath)) {
                                @unlink($filePath);
                            }
                            $fichier->delete();
                        }
                        // Supprimer les réactions
                        $message->reactions()->delete();
                        $message->delete();
                    }
                    // Supprimer tous les fichiers restants dans le dossier messages/ liés à ce groupe (sécurité)
                    $msgDir = storage_path('app/public/messages/');
                    foreach (glob($msgDir . $groupe->id_groupe_message . '_*') as $file) {
                        @unlink($file);
                    }
                    $groupe->delete();
                } else {
                    // Supprimer les messages de la personne dans ce groupe
                    $messages = $groupe->messages()->where('id_auteur', $personne->id_personne)->get();
                    foreach ($messages as $message) {
                        // Supprimer les fichiers liés à ce message
                        foreach ($message->fichiers as $fichier) {
                            $filePath = storage_path('app/public/messages/' . $fichier->chemin_fichier);
                            if (file_exists($filePath)) {
                                @unlink($filePath);
                            }
                            $fichier->delete();
                        }
                        // Supprimer les réactions
                        $message->reactions()->delete();
                        $message->delete();
                    }
                }
            }

            // Nettoyage final : supprimer tous les fichiers avatars/messages qui contiennent l'id ou le nom de la personne (sécurité)
            $avatarDir = storage_path('app/public/avatars/');
            foreach (glob($avatarDir . '*') as $file) {
                if (strpos(basename($file), (string)$personne->id_personne) !== false || (isset($personne->photo_profil) && basename($file) === $personne->photo_profil)) {
                    @unlink($file);
                }
            }
            $msgDir = storage_path('app/public/messages/');
            foreach (glob($msgDir . '*') as $file) {
                if (strpos(basename($file), (string)$personne->id_personne) !== false) {
                    @unlink($file);
                }
            }

            // Supprimer les rôles (admin, gardien, resident)
            if ($personne->admin) $personne->admin->delete();
            if ($personne->gardien) $personne->gardien->delete();
            if ($personne->resident) $personne->resident->delete();

            // Supprimer la personne
            $personne->delete();
            $this->logAction($user ? $user->id_personne : null, 'delete_person', 'Suppression de l\'utilisateur: ' . $personne->email, $request);
            return response()->json(['success' => true]);
        }
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
                $this->logAction(null, 'login_attempt', 'Tentative de connexion pour: ' . $request->email, $request);

                $request->validate([
                    'email' => 'required|email',
                    'password' => 'required|string',
                ]);

                // Trouver l'utilisateur
                $user = Personne::where('email', $request->email)->first();

                if (!$user) {
                    Log::warning('Utilisateur non trouvé: ' . $request->email);
                    $this->logAction(null, 'login_failed', 'Utilisateur non trouvé: ' . $request->email, $request);
                    return response()->json([
                        'success' => false,
                        'message' => 'Utilisateur non trouvé'
                    ], 404);
                }

                if (!Hash::check($request->password, $user->mot_de_passe)) {
                    Log::warning('Mot de passe incorrect pour: ' . $request->email);
                    $this->logAction($user->id_personne, 'login_failed', 'Mot de passe incorrect pour: ' . $request->email, $request);
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
                $this->logAction($user->id_personne, 'login', 'Connexion réussie', $request);

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
                $this->logAction(null, 'login_validation_error', 'Erreur de validation lors de la connexion: ' . json_encode($e->errors()), $request);
                return response()->json([
                    'success' => false,
                    'message' => 'Données de validation incorrectes',
                    'errors' => $e->errors()
                ], 422);
            } catch (\Exception $e) {
                Log::error('Erreur de connexion: ' . $e->getMessage());
                $this->logAction(null, 'login_exception', 'Erreur lors de la connexion: ' . $e->getMessage(), $request);
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
                $user = $request->user();
                if ($user && $user->currentAccessToken()) {
                    $user->currentAccessToken()->delete();
                    $this->logAction($user->id_personne, 'logout', 'Déconnexion réussie', $request);
                }
                // Toujours retourner succès, même si pas authentifié ou déjà déconnecté
                return response()->json([
                    'success' => true,
                    'message' => 'Déconnexion réussie'
                ]);
            } catch (\Exception $e) {
                // Même en cas d'erreur, retourner succès (idempotence)
                return response()->json([
                    'success' => true,
                    'message' => 'Déconnexion réussie (aucun token actif)'
                ]);
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
         * Obtenir le profil utilisateur
         */
        public function getProfile(Request $request)
        {
            try {
                $user = $this->getCurrentUser($request);
                
                if (!$user) {
                    return response()->json([
                        'success' => false,
                        'message' => 'Utilisateur non authentifié'
                    ], 401);
                }

                return response()->json([
                    'success' => true,
                    'user' => [
                        'id_personne' => $user->id_personne,
                        'email' => $user->email,
                        'nom' => $user->nom,
                        'prenom' => $user->prenom,
                        'nom_complet' => $user->nom_complet,
                        'numero_telephone' => $user->numero_telephone,
                        'photo_profil' => $user->photo_profil
                    ]
                ]);

            } catch (\Exception $e) {
                Log::error('Erreur lors de la récupération du profil: ' . $e->getMessage());
                
                return response()->json([
                    'success' => false,
                    'message' => 'Erreur lors de la récupération du profil',
                    'error' => $e->getMessage()
                ], 500);
            }
        }

        /**
         * Obtenir les statistiques du profil utilisateur
         */
        public function getProfileStats(Request $request)
        {
            try {
                Log::info('Starting getProfileStats method');
                $user = $this->getCurrentUser($request);
                
                if (!$user) {
                    Log::warning('User not authenticated in getProfileStats');
                    return response()->json([
                        'success' => false,
                        'message' => 'Utilisateur non authentifié'
                    ], 401);
                }

                Log::info('User authenticated successfully in getProfileStats', ['user_id' => $user->id_personne]);

                // Messages envoyés par l'utilisateur avec tendance
                Log::info('Starting messages count query');
                $messagesEnvoyes = DB::table('message')
                    ->where('id_auteur', $user->id_personne)
                    ->count();
                Log::info('Messages sent count: ' . $messagesEnvoyes);
                
                $messagesCeMois = DB::table('message')
                    ->where('id_auteur', $user->id_personne)
                    ->whereMonth('date_envoi', now()->month)
                    ->whereYear('date_envoi', now()->year)
                    ->count();

                $messagesMoisPasse = DB::table('message')
                    ->where('id_auteur', $user->id_personne)
                    ->whereMonth('date_envoi', now()->subMonth()->month)
                    ->whereYear('date_envoi', now()->subMonth()->year)
                    ->count();

                // Visites programmées/organisées (pour les invités principalement)
                $visitesTotal = DB::table('visite')
                    ->where('id_invite', $user->id_personne)
                    ->count();

                $visitesEnCours = DB::table('visite')
                    ->where('id_invite', $user->id_personne)
                    ->where('statut_visite', 'en_cours')
                    ->count();

                $visitesTerminees = DB::table('visite')
                    ->where('id_invite', $user->id_personne)
                    ->where('statut_visite', 'terminee')
                    ->count();

                $visitesAnnulees = DB::table('visite')
                    ->where('id_invite', $user->id_personne)
                    ->where('statut_visite', 'annulee')
                    ->count();

                // Incidents signalés (si c'est un résident/gardien)
                $incidentsSignales = DB::table('incident')
                    ->where('id_signaleur', $user->id_personne)
                    ->count();

                $incidentsResolus = DB::table('incident')
                    ->where('id_signaleur', $user->id_personne)
                    ->where('statut', 'resolu')
                    ->count();

                // Groupes et participation
                $groupesParticipes = DB::table('personne_groupe')
                    ->where('id_personne', $user->id_personne)
                    ->count();

                $groupesActifs = DB::table('personne_groupe')
                    ->join('message', 'personne_groupe.id_groupe_message', '=', 'message.id_groupe_message')
                    ->where('personne_groupe.id_personne', $user->id_personne)
                    ->where('message.date_envoi', '>=', now()->subWeek())
                    ->distinct('personne_groupe.id_groupe_message')
                    ->count();

                // Messages non lus et activité récente
                $messagesNonLus = DB::table('message')
                    ->join('personne_groupe', 'message.id_groupe_message', '=', 'personne_groupe.id_groupe_message')
                    ->where('personne_groupe.id_personne', $user->id_personne)
                    ->where('message.id_auteur', '!=', $user->id_personne)
                    ->where(function($query) {
                        $query->whereColumn('message.date_envoi', '>', 'personne_groupe.derniere_connexion')
                            ->orWhereNull('personne_groupe.derniere_connexion');
                    })
                    ->count();

                // Temps de réponse moyen
                $tempsReponse = DB::table('message as m1')
                    ->join('message as m2', function($join) use ($user) {
                        $join->on('m1.id_groupe_message', '=', 'm2.id_groupe_message')
                             ->where('m1.id_auteur', '!=', $user->id_personne)
                             ->where('m2.id_auteur', $user->id_personne)
                             ->whereColumn('m2.date_envoi', '>', 'm1.date_envoi');
                    })
                    ->selectRaw('AVG(TIMESTAMPDIFF(MINUTE, m1.date_envoi, m2.date_envoi)) as temps_moyen')
                    ->value('temps_moyen');

                // Déterminer le rôle avec informations supplémentaires
                $roleData = ['type' => 'Résident', 'details' => []];
                
                if (DB::table('admin')->where('id_personne', $user->id_personne)->exists()) {
                    $admin = DB::table('admin')->where('id_personne', $user->id_personne)->first();
                    $roleData = [
                        'type' => 'Administrateur',
                        'details' => [
                            'niveau_acces' => $admin->niveau_acces,
                            'date_nomination' => $admin->date_nomination,
                            'actions_admin' => DB::table('logs')->where('user_id', $user->id_personne)->count()
                        ]
                    ];
                } elseif (DB::table('gardien')->where('id_personne', $user->id_personne)->exists()) {
                    $roleData = [
                        'type' => 'Gardien',
                        'details' => [
                            // Gardien specific statistics could be added here when relevant columns exist
                        ]
                    ];
                } elseif (DB::table('invite')->where('id_personne', $user->id_personne)->exists()) {
                    $invite = DB::table('invite')->where('id_personne', $user->id_personne)->first();
                    $roleData = [
                        'type' => 'Invité',
                        'details' => [
                            'date_debut_sejour' => $invite->date_debut_sejour,
                            'date_fin_sejour' => $invite->date_fin_sejour,
                            'statut_invitation' => $invite->statut
                        ]
                    ];
                } else {
                    $resident = DB::table('resident')->where('id_personne', $user->id_personne)->first();
                    if ($resident) {
                        $roleData = [
                            'type' => 'Résident',
                            'details' => [
                                'adresse_logement' => $resident->adresse_logement,
                                'date_emmenagement' => $resident->date_emmenagement
                            ]
                        ];
                    }
                }

                // Dates importantes
                $dateInscription = DB::table('personne_groupe')
                    ->where('id_personne', $user->id_personne)
                    ->orderBy('date_adhesion', 'asc')
                    ->value('date_adhesion') ?: $user->created_at;

                $derniereConnexion = DB::table('personne_groupe')
                    ->where('id_personne', $user->id_personne)
                    ->orderBy('derniere_connexion', 'desc')
                    ->value('derniere_connexion');

                // Score d'activité (0-100) - Nouveau calcul incluant les visites
                $scoreActivite = min(100, (
                    ($messagesEnvoyes * 2) +
                    ($visitesTotal * 3) +
                    ($groupesActifs * 5) +
                    ($incidentsSignales * 3) +
                    ($visitesTerminees * 2)
                ) / 15);

                return response()->json([
                    'success' => true,
                    'data' => [
                        // Statistiques de base
                        'messages_envoyes' => $messagesEnvoyes,
                        'messages_ce_mois' => $messagesCeMois,
                        'tendance_messages' => $messagesCeMois - $messagesMoisPasse,
                        
                        // Visites (nouvelle section principale)
                        'visites_total' => $visitesTotal,
                        'visites_en_cours' => $visitesEnCours,
                        'visites_terminees' => $visitesTerminees,
                        'visites_annulees' => $visitesAnnulees,
                        'taux_reussite_visites' => $visitesTotal > 0 ? round(($visitesTerminees / $visitesTotal) * 100) : 0,
                        
                        // Activité et engagement
                        'groupes_participes' => $groupesParticipes,
                        'groupes_actifs' => $groupesActifs,
                        'score_activite' => round($scoreActivite),
                        'temps_reponse_moyen' => $tempsReponse ? round($tempsReponse) : null,
                        
                        // Incidents et responsabilités
                        'incidents_signales' => $incidentsSignales,
                        'incidents_resolus' => $incidentsResolus,
                        'taux_resolution' => $incidentsSignales > 0 ? round(($incidentsResolus / $incidentsSignales) * 100) : 0,
                        
                        // Informations temporelles
                        'derniere_connexion' => $derniereConnexion,
                        'date_inscription' => $dateInscription,
                        'anciennete_jours' => $dateInscription ? now()->diffInDays($dateInscription) : 0,
                        
                        // Rôle et détails
                        'role' => $roleData['type'],
                        'role_details' => $roleData['details'],
                        
                        // Badges et accomplissements
                        'badges' => $this->generateUserBadges($user, [
                            'messages_envoyes' => $messagesEnvoyes,
                            'visites_total' => $visitesTotal,
                            'visites_terminees' => $visitesTerminees,
                            'groupes_participes' => $groupesParticipes,
                            'incidents_signales' => $incidentsSignales,
                            'score_activite' => $scoreActivite
                        ])
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
         * Génère des badges d'accomplissement pour l'utilisateur
         */
        private function generateUserBadges($user, $stats)
        {
            $badges = [];

            // Badge de communication
            if ($stats['messages_envoyes'] >= 100) {
                $badges[] = ['nom' => 'Grand Communicateur', 'icone' => '💬', 'couleur' => 'blue'];
            } elseif ($stats['messages_envoyes'] >= 50) {
                $badges[] = ['nom' => 'Communicateur', 'icone' => '📢', 'couleur' => 'green'];
            } elseif ($stats['messages_envoyes'] >= 10) {
                $badges[] = ['nom' => 'Première Voix', 'icone' => '🗣️', 'couleur' => 'yellow'];
            }

            // Badge de visites (nouveau)
            if ($stats['visites_total'] >= 20) {
                $badges[] = ['nom' => 'Organisateur Expert', 'icone' => '🎯', 'couleur' => 'purple'];
            } elseif ($stats['visites_total'] >= 10) {
                $badges[] = ['nom' => 'Bon Organisateur', 'icone' => '📅', 'couleur' => 'blue'];
            } elseif ($stats['visites_total'] >= 5) {
                $badges[] = ['nom' => 'Organisateur', 'icone' => '�️', 'couleur' => 'green'];
            }

            // Badge de réussite des visites
            if ($stats['visites_terminees'] >= 10) {
                $badges[] = ['nom' => 'Hôte Accompli', 'icone' => '🏆', 'couleur' => 'yellow'];
            } elseif ($stats['visites_terminees'] >= 5) {
                $badges[] = ['nom' => 'Bon Hôte', 'icone' => '👨‍�', 'couleur' => 'green'];
            }

            // Badge de sociabilité
            if ($stats['groupes_participes'] >= 5) {
                $badges[] = ['nom' => 'Sociable', 'icone' => '👥', 'couleur' => 'green'];
            }

            // Badge d'activité
            if ($stats['score_activite'] >= 80) {
                $badges[] = ['nom' => 'Très Actif', 'icone' => '⚡', 'couleur' => 'yellow'];
            } elseif ($stats['score_activite'] >= 50) {
                $badges[] = ['nom' => 'Actif', 'icone' => '✨', 'couleur' => 'blue'];
            }

            // Badge de vigilance
            if ($stats['incidents_signales'] >= 5) {
                $badges[] = ['nom' => 'Vigilant', 'icone' => '👁️', 'couleur' => 'orange'];
            }

            return $badges;
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
?>


