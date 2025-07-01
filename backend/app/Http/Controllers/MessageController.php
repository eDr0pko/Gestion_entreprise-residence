<?php

namespace App\Http\Controllers;

use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\Log;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Storage;
use App\Models\GroupeMessage;
use App\Models\Message;
use App\Models\Personne;

class MessageController extends Controller
{
    // Récupérer les groupes de conversation de l'utilisateur connecté
    public function getConversations()
    {
        try {
            $user = Auth::user();
            
            if (!$user) {
                return response()->json([
                    'success' => false,
                    'message' => 'Utilisateur non authentifié'
                ], 401);
            }

            Log::info('Chargement des conversations pour l\'utilisateur: ' . $user->email);
            
            // Récupérer les groupes avec le dernier message et le compteur de messages non lus
            $conversations = GroupeMessage::whereHas('personnes', function ($query) use ($user) {
                $query->where('email_personne', $user->email);
            })
            ->with(['dernierMessage.auteur'])
            ->get()
            ->map(function ($groupe) use ($user) {
                $dernierMessage = $groupe->dernierMessage;
                
                // Récupérer la dernière connexion de l'utilisateur pour ce groupe
                $personneGroupe = DB::table('personne_groupe')
                    ->where('email_personne', $user->email)
                    ->where('id_groupe_message', $groupe->id_groupe_message)
                    ->first();
                
                $derniereConnexion = $personneGroupe ? $personneGroupe->derniere_connexion : null;
                
                // Compter les messages non lus (envoyés après la dernière connexion)
                $messagesNonLus = 0;
                if ($derniereConnexion) {
                    $messagesNonLus = Message::where('id_groupe_message', $groupe->id_groupe_message)
                        ->where('date_envoi', '>', $derniereConnexion)
                        ->where('email_auteur', '!=', $user->email) // Exclure ses propres messages
                        ->count();
                } else {
                    // Si jamais connecté, tous les messages sont non lus (sauf les siens)
                    $messagesNonLus = Message::where('id_groupe_message', $groupe->id_groupe_message)
                        ->where('email_auteur', '!=', $user->email)
                        ->count();
                }
                
                return [
                    'id_groupe_message' => $groupe->id_groupe_message,
                    'nom_groupe' => $groupe->nom_groupe,
                    'date_creation' => $groupe->date_creation ? $groupe->date_creation->toISOString() : now()->toISOString(),
                    'derniere_activite' => $dernierMessage && $dernierMessage->date_envoi ? $dernierMessage->date_envoi->toISOString() : $groupe->date_creation->toISOString(),
                    'dernier_contenu' => $dernierMessage ? $dernierMessage->contenu_message : null,
                    'dernier_auteur' => $dernierMessage && $dernierMessage->auteur ? $dernierMessage->auteur->nom_complet : null,
                    'nombre_membres' => $groupe->personnes()->count(),
                    'messages_non_lus' => $messagesNonLus,
                    'derniere_connexion' => $derniereConnexion,
                ];
            })
            ->sortByDesc('derniere_activite')
            ->values();

            Log::info('Conversations trouvées: ' . count($conversations));

            return response()->json([
                'success' => true,
                'conversations' => $conversations
            ]);
        } catch (\Exception $e) {
            Log::error('Erreur lors de la récupération des conversations: ' . $e->getMessage());
            Log::error('Stack trace: ' . $e->getTraceAsString());
            
            return response()->json([
                'success' => false,
                'message' => 'Erreur lors de la récupération des conversations',
                'error' => $e->getMessage()
            ], 500);
        }
    }

    // Récupérer les messages d'un groupe et marquer comme lu
    public function getMessages($groupId)
    {
        try {
            $user = Auth::user();
            
            if (!$user) {
                return response()->json([
                    'success' => false,
                    'message' => 'Utilisateur non authentifié'
                ], 401);
            }

            // Vérifier que l'utilisateur fait partie du groupe
            $groupe = GroupeMessage::whereHas('personnes', function ($query) use ($user) {
                $query->where('email_personne', $user->email);
            })->find($groupId);

            if (!$groupe) {
                return response()->json([
                    'success' => false,
                    'message' => 'Accès non autorisé à ce groupe'
                ], 403);
            }

            // Récupérer les messages avec réactions et fichiers
            $messages = DB::table('message')
                ->join('personne', 'message.email_auteur', '=', 'personne.email')
                ->leftJoin('message_fichier', 'message.id_message', '=', 'message_fichier.id_message')
                ->where('message.id_groupe_message', $groupId)
                ->select(
                    'message.id_message',
                    'message.contenu_message',
                    'message.date_envoi',
                    'message.email_auteur',
                    'message.a_fichiers',
                    'personne.nom',
                    'personne.prenom',
                    DB::raw('CONCAT(personne.prenom, " ", personne.nom) as auteur_nom')
                )
                ->groupBy('message.id_message', 'message.contenu_message', 'message.date_envoi', 'message.email_auteur', 'message.a_fichiers', 'personne.nom', 'personne.prenom')
                ->orderBy('message.date_envoi', 'asc')
                ->get();

            $messagesArray = [];
            foreach ($messages as $message) {
                // Récupérer les réactions pour ce message
                $reactions = DB::table('message_reaction')
                    ->join('personne', 'message_reaction.email_personne', '=', 'personne.email')
                    ->where('message_reaction.id_message', $message->id_message)
                    ->select(
                        'message_reaction.emoji',
                        'message_reaction.email_personne',
                        'personne.prenom',
                        'personne.nom',
                        'message_reaction.date_reaction'
                    )
                    ->get()
                    ->groupBy('emoji')
                    ->map(function ($reactionGroup) {
                        return [
                            'count' => $reactionGroup->count(),
                            'users' => $reactionGroup->map(function ($reaction) {
                                return [
                                    'email' => $reaction->email_personne,
                                    'nom' => $reaction->prenom . ' ' . $reaction->nom
                                ];
                            })->toArray()
                        ];
                    });

                // Récupérer les fichiers pour ce message
                $fichiers = [];
                if ($message->a_fichiers) {
                    $fichiers = DB::table('message_fichier')
                        ->where('id_message', $message->id_message)
                        ->select('id_fichier', 'nom_original', 'type_fichier', 'taille_fichier', 'chemin_fichier')
                        ->get()
                        ->toArray();
                }

                // Récupérer le statut de lecture pour l'utilisateur actuel
                $statut = DB::table('message_statut')
                    ->where('id_message', $message->id_message)
                    ->where('email_personne', $user->email)
                    ->value('statut') ?? 'recu';

                $messagesArray[] = [
                    'id_message' => $message->id_message,
                    'contenu_message' => $message->contenu_message,
                    'date_envoi' => $message->date_envoi,
                    'email_auteur' => $message->email_auteur,
                    'auteur_nom' => $message->auteur_nom,
                    'is_current_user' => $message->email_auteur === $user->email,
                    'reactions' => $reactions,
                    'fichiers' => $fichiers,
                    'statut_lecture' => $statut,
                    'a_fichiers' => (bool)$message->a_fichiers
                ];
            }

            // Marquer tous les messages comme lus pour cet utilisateur
            $this->markMessagesAsRead($groupId, $user->email);

            return response()->json([
                'success' => true,
                'messages' => $messagesArray
            ]);

        } catch (\Exception $e) {
            Log::error('Erreur lors de la récupération des messages: ' . $e->getMessage());
            
            return response()->json([
                'success' => false,
                'message' => 'Erreur lors de la récupération des messages',
                'error' => $e->getMessage()
            ], 500);
        }
    }

    // Marquer une conversation comme lue (endpoint séparé si besoin)
    public function markAsRead($groupId)
    {
        try {
            $user = Auth::user();
            
            if (!$user) {
                return response()->json([
                    'success' => false,
                    'message' => 'Utilisateur non authentifié'
                ], 401);
            }

            // Vérifier que l'utilisateur fait partie du groupe
            $groupe = GroupeMessage::whereHas('personnes', function ($query) use ($user) {
                $query->where('email_personne', $user->email);
            })->find($groupId);

            if (!$groupe) {
                return response()->json([
                    'success' => false,
                    'message' => 'Accès non autorisé à ce groupe'
                ], 403);
            }

            // Mettre à jour la dernière connexion
            DB::table('personne_groupe')
                ->where('email_personne', $user->email)
                ->where('id_groupe_message', $groupId)
                ->update(['derniere_connexion' => now()]);

            return response()->json([
                'success' => true,
                'message' => 'Conversation marquée comme lue'
            ]);
        } catch (\Exception $e) {
            Log::error('Erreur lors du marquage comme lu: ' . $e->getMessage());
            
            return response()->json([
                'success' => false,
                'message' => 'Erreur lors du marquage comme lu',
                'error' => $e->getMessage()
            ], 500);
        }
    }

    // Envoyer un message
    public function sendMessage(Request $request, $groupId)
    {
        try {
            $user = Auth::user();
            
            if (!$user) {
                return response()->json([
                    'success' => false,
                    'message' => 'Utilisateur non authentifié'
                ], 401);
            }

            $request->validate([
                'contenu' => 'nullable|string|max:5000',
                'fichiers' => 'nullable|array|max:5',
                'fichiers.*' => 'file|max:10240' // 10MB max par fichier
            ]);

            // Vérifier qu'il y a soit du contenu soit des fichiers
            if (empty($request->contenu) && empty($request->file('fichiers'))) {
                return response()->json([
                    'success' => false,
                    'message' => 'Le message doit contenir du texte ou des fichiers'
                ], 400);
            }

            // Vérifier l'accès au groupe
            $groupe = GroupeMessage::whereHas('personnes', function ($query) use ($user) {
                $query->where('email_personne', $user->email);
            })->find($groupId);

            if (!$groupe) {
                return response()->json([
                    'success' => false,
                    'message' => 'Accès non autorisé à ce groupe'
                ], 403);
            }

            DB::beginTransaction();
            
            try {
                // Créer le message
                $message = Message::create([
                    'id_groupe_message' => $groupId,
                    'email_auteur' => $user->email,
                    'contenu_message' => $request->contenu ?? '',
                    'date_envoi' => now(),
                    'a_fichiers' => $request->hasFile('fichiers')
                ]);

                $fichiers = [];
                
                // Traiter les fichiers uploadés
                if ($request->hasFile('fichiers')) {
                    foreach ($request->file('fichiers') as $fichier) {
                        $nomOriginal = $fichier->getClientOriginalName();
                        $extension = $fichier->getClientOriginalExtension();
                        $nomFichier = time() . '_' . uniqid() . '.' . $extension;
                        
                        // Stocker le fichier
                        $cheminFichier = $fichier->storeAs('messages', $nomFichier, 'public');
                        
                        // Enregistrer en base
                        $fichierDb = DB::table('message_fichier')->insertGetId([
                            'id_message' => $message->id_message,
                            'nom_fichier' => $nomFichier,
                            'nom_original' => $nomOriginal,
                            'chemin_fichier' => $cheminFichier,
                            'type_fichier' => $fichier->getMimeType(),
                            'taille_fichier' => $fichier->getSize(),
                            'date_upload' => now()
                        ]);

                        $fichiers[] = [
                            'id_fichier' => $fichierDb,
                            'nom_original' => $nomOriginal,
                            'type_fichier' => $fichier->getMimeType(),
                            'taille_fichier' => $fichier->getSize(),
                            'chemin_fichier' => $cheminFichier
                        ];
                    }
                }

                // Créer les statuts de lecture pour tous les membres du groupe
                $membres = DB::table('personne_groupe')
                    ->where('id_groupe_message', $groupId)
                    ->pluck('email_personne');

                foreach ($membres as $membre) {
                    DB::table('message_statut')->insert([
                        'id_message' => $message->id_message,
                        'email_personne' => $membre,
                        'statut' => $membre === $user->email ? 'envoye' : 'recu',
                        'date_statut' => now()
                    ]);
                }

                DB::commit();

                return response()->json([
                    'success' => true,
                    'message' => [
                        'id_message' => $message->id_message,
                        'contenu_message' => $message->contenu_message,
                        'date_envoi' => $message->date_envoi->toISOString(),
                        'email_auteur' => $message->email_auteur,
                        'auteur_nom' => $user->nom_complet,
                        'is_current_user' => true,
                        'reactions' => [],
                        'fichiers' => $fichiers,
                        'statut_lecture' => 'envoye',
                        'a_fichiers' => !empty($fichiers)
                    ]
                ]);

            } catch (\Exception $e) {
                DB::rollBack();
                throw $e;
            }

        } catch (\Exception $e) {
            Log::error('Erreur lors de l\'envoi du message: ' . $e->getMessage());
            
            return response()->json([
                'success' => false,
                'message' => 'Erreur lors de l\'envoi du message',
                'error' => $e->getMessage()
            ], 500);
        }
    }

    // Ajouter une réaction à un message
    public function addReaction(Request $request, $messageId)
    {
        try {
            $user = Auth::user();
            
            if (!$user) {
                return response()->json([
                    'success' => false,
                    'message' => 'Utilisateur non authentifié'
                ], 401);
            }

            $request->validate([
                'emoji' => 'required|string|max:10'
            ]);

            // Vérifier que le message existe et que l'utilisateur a accès
            $message = DB::table('message')
                ->join('personne_groupe', 'message.id_groupe_message', '=', 'personne_groupe.id_groupe_message')
                ->where('message.id_message', $messageId)
                ->where('personne_groupe.email_personne', $user->email)
                ->select('message.*')
                ->first();

            if (!$message) {
                return response()->json([
                    'success' => false,
                    'message' => 'Message non trouvé ou accès non autorisé'
                ], 404);
            }

            // Ajouter ou supprimer la réaction
            $existingReaction = DB::table('message_reaction')
                ->where('id_message', $messageId)
                ->where('email_personne', $user->email)
                ->where('emoji', $request->emoji)
                ->first();

            if ($existingReaction) {
                // Supprimer la réaction si elle existe déjà
                DB::table('message_reaction')
                    ->where('id_reaction', $existingReaction->id_reaction)
                    ->delete();
                $action = 'removed';
            } else {
                // Ajouter la réaction
                DB::table('message_reaction')->insert([
                    'id_message' => $messageId,
                    'email_personne' => $user->email,
                    'emoji' => $request->emoji,
                    'date_reaction' => now()
                ]);
                $action = 'added';
            }

            // Récupérer les réactions mises à jour
            $reactions = DB::table('message_reaction')
                ->join('personne', 'message_reaction.email_personne', '=', 'personne.email')
                ->where('message_reaction.id_message', $messageId)
                ->select(
                    'message_reaction.emoji',
                    'message_reaction.email_personne',
                    'personne.prenom',
                    'personne.nom'
                )
                ->get()
                ->groupBy('emoji')
                ->map(function ($reactionGroup) {
                    return [
                        'count' => $reactionGroup->count(),
                        'users' => $reactionGroup->map(function ($reaction) {
                            return [
                                'email' => $reaction->email_personne,
                                'nom' => $reaction->prenom . ' ' . $reaction->nom
                            ];
                        })->toArray()
                    ];
                });

            return response()->json([
                'success' => true,
                'action' => $action,
                'reactions' => $reactions
            ]);

        } catch (\Exception $e) {
            Log::error('Erreur lors de l\'ajout de réaction: ' . $e->getMessage());
            
            return response()->json([
                'success' => false,
                'message' => 'Erreur lors de l\'ajout de réaction',
                'error' => $e->getMessage()
            ], 500);
        }
    }

    // Télécharger un fichier
    public function downloadFile($fichierId)
    {
        try {
            $user = Auth::user();
            
            if (!$user) {
                return response()->json([
                    'success' => false,
                    'message' => 'Utilisateur non authentifié'
                ], 401);
            }

            // Vérifier l'accès au fichier
            $fichier = DB::table('message_fichier')
                ->join('message', 'message_fichier.id_message', '=', 'message.id_message')
                ->join('personne_groupe', 'message.id_groupe_message', '=', 'personne_groupe.id_groupe_message')
                ->where('message_fichier.id_fichier', $fichierId)
                ->where('personne_groupe.email_personne', $user->email)
                ->select('message_fichier.*')
                ->first();

            if (!$fichier) {
                return response()->json([
                    'success' => false,
                    'message' => 'Fichier non trouvé ou accès non autorisé'
                ], 404);
            }

            $cheminComplet = storage_path('app/public/' . $fichier->chemin_fichier);
            
            if (!file_exists($cheminComplet)) {
                return response()->json([
                    'success' => false,
                    'message' => 'Fichier introuvable sur le serveur'
                ], 404);
            }

            return response()->download($cheminComplet, $fichier->nom_original);

        } catch (\Exception $e) {
            Log::error('Erreur lors du téléchargement: ' . $e->getMessage());
            
            return response()->json([
                'success' => false,
                'message' => 'Erreur lors du téléchargement',
                'error' => $e->getMessage()
            ], 500);
        }
    }

    // Marquer les messages comme lus
    private function markMessagesAsRead($groupId, $userEmail)
    {
        DB::table('message_statut')
            ->join('message', 'message_statut.id_message', '=', 'message.id_message')
            ->where('message.id_groupe_message', $groupId)
            ->where('message_statut.email_personne', $userEmail)
            ->where('message_statut.statut', '!=', 'lu')
            ->update([
                'message_statut.statut' => 'lu',
                'message_statut.date_statut' => now()
            ]);
    }

    // Créer une nouvelle conversation
    public function createConversation(Request $request)
    {
        try {
            $user = Auth::user();
            
            if (!$user) {
                return response()->json([
                    'success' => false,
                    'message' => 'Utilisateur non authentifié'
                ], 401);
            }

            $request->validate([
                'nom_groupe' => 'required|string|max:100',
                'participants' => 'required|array|min:1',
                'participants.*' => 'required|email|exists:personne,email'
            ]);

            Log::info('Création d\'une nouvelle conversation par: ' . $user->email);
            
            // Vérifier que l'utilisateur ne s'ajoute pas lui-même dans les participants
            $participants = collect($request->participants);
            if (!$participants->contains($user->email)) {
                // Ajouter automatiquement l'utilisateur actuel
                $participants->push($user->email);
            }

            // Supprimer les doublons
            $participants = $participants->unique();

            DB::beginTransaction();
            
            try {
                // Créer le groupe
                $groupe = GroupeMessage::create([
                    'nom_groupe' => $request->nom_groupe,
                    'date_creation' => now()
                ]);

                // Ajouter tous les participants au groupe
                foreach ($participants as $participantEmail) {
                    DB::table('personne_groupe')->insert([
                        'email_personne' => $participantEmail,
                        'id_groupe_message' => $groupe->id_groupe_message,
                        'date_adhesion' => now(),
                        'derniere_connexion' => $participantEmail === $user->email ? now() : null
                    ]);
                }

                // Envoyer un message de bienvenue automatique
                $messageContenu = "Conversation créée par " . $user->nom_complet . ". Bienvenue à tous !";
                
                Message::create([
                    'id_groupe_message' => $groupe->id_groupe_message,
                    'email_auteur' => $user->email,
                    'contenu_message' => $messageContenu,
                    'date_envoi' => now()
                ]);

                DB::commit();

                Log::info('Conversation créée avec succès: ' . $groupe->id_groupe_message);

                // Retourner les informations de la conversation créée
                $conversationData = [
                    'id_groupe_message' => $groupe->id_groupe_message,
                    'nom_groupe' => $groupe->nom_groupe,
                    'date_creation' => $groupe->date_creation->toISOString(),
                    'derniere_activite' => $groupe->date_creation->toISOString(),
                    'dernier_contenu' => $messageContenu,
                    'dernier_auteur' => $user->nom_complet,
                    'nombre_membres' => $participants->count(),
                    'messages_non_lus' => 0,
                    'derniere_connexion' => now()->toISOString(),
                ];

                return response()->json([
                    'success' => true,
                    'message' => 'Conversation créée avec succès',
                    'conversation' => $conversationData
                ]);

            } catch (\Exception $e) {
                DB::rollBack();
                throw $e;
            }
            
        } catch (\Exception $e) {
            Log::error('Erreur lors de la création de la conversation: ' . $e->getMessage());
            Log::error('Stack trace: ' . $e->getTraceAsString());
            
            return response()->json([
                'success' => false,
                'message' => 'Erreur lors de la création de la conversation',
                'error' => $e->getMessage()
            ], 500);
        }
    }

    // Récupérer la liste des utilisateurs disponibles pour créer une conversation
    public function getAvailableUsers()
    {
        try {
            $user = Auth::user();
            
            if (!$user) {
                return response()->json([
                    'success' => false,
                    'message' => 'Utilisateur non authentifié'
                ], 401);
            }

            // Récupérer tous les utilisateurs sauf l'utilisateur actuel
            $users = Personne::where('email', '!=', $user->email)
                ->select('email', 'nom', 'prenom')
                ->get()
                ->map(function ($personne) {
                    return [
                        'email' => $personne->email,
                        'nom_complet' => $personne->nom_complet,
                        'nom' => $personne->nom,
                        'prenom' => $personne->prenom,
                        'role' => $this->getUserRole($personne->email)
                    ];
                })
                ->sortBy('nom_complet')
                ->values();

            return response()->json([
                'success' => true,
                'users' => $users
            ]);
            
        } catch (\Exception $e) {
            Log::error('Erreur lors de la récupération des utilisateurs: ' . $e->getMessage());
            
            return response()->json([
                'success' => false,
                'message' => 'Erreur lors de la récupération des utilisateurs',
                'error' => $e->getMessage()
            ], 500);
        }
    }

    // Récupérer les membres d'un groupe
    public function getGroupMembers($groupId)
    {
        try {
            $user = Auth::user();
            
            if (!$user) {
                return response()->json([
                    'success' => false,
                    'message' => 'Utilisateur non authentifié'
                ], 401);
            }

            // Vérifier que l'utilisateur fait partie du groupe
            $groupe = GroupeMessage::whereHas('personnes', function ($query) use ($user) {
                $query->where('email_personne', $user->email);
            })->find($groupId);

            if (!$groupe) {
                return response()->json([
                    'success' => false,
                    'message' => 'Accès non autorisé à ce groupe'
                ], 403);
            }

            // Récupérer les membres du groupe
            $members = DB::table('personne_groupe')
                ->join('personne', 'personne_groupe.email_personne', '=', 'personne.email')
                ->where('personne_groupe.id_groupe_message', $groupId)
                ->select(
                    'personne.email',
                    'personne.nom',
                    'personne.prenom',
                    'personne_groupe.date_adhesion'
                )
                ->get()
                ->map(function ($member) use ($user) {
                    return [
                        'email' => $member->email,
                        'nom' => $member->nom,
                        'prenom' => $member->prenom,
                        'nom_complet' => trim($member->prenom . ' ' . $member->nom),
                        'role' => $this->getUserRole($member->email),
                        'is_current_user' => $member->email === $user->email,
                        'date_adhesion' => $member->date_adhesion
                    ];
                })
                ->sortBy('nom_complet')
                ->values();

            return response()->json([
                'success' => true,
                'members' => $members
            ]);
            
        } catch (\Exception $e) {
            Log::error('Erreur lors de la récupération des membres: ' . $e->getMessage());
            
            return response()->json([
                'success' => false,
                'message' => 'Erreur lors de la récupération des membres',
                'error' => $e->getMessage()
            ], 500);
        }
    }

    // Ajouter des membres à un groupe
    public function addGroupMembers(Request $request, $groupId)
    {
        try {
            $user = Auth::user();
            
            if (!$user) {
                return response()->json([
                    'success' => false,
                    'message' => 'Utilisateur non authentifié'
                ], 401);
            }

            $request->validate([
                'members' => 'required|array|min:1',
                'members.*' => 'required|email|exists:personne,email'
            ]);

            // Vérifier que l'utilisateur fait partie du groupe
            $groupe = GroupeMessage::whereHas('personnes', function ($query) use ($user) {
                $query->where('email_personne', $user->email);
            })->find($groupId);

            if (!$groupe) {
                return response()->json([
                    'success' => false,
                    'message' => 'Accès non autorisé à ce groupe'
                ], 403);
            }

            $newMembers = collect($request->members);
            $addedMembers = [];

            DB::beginTransaction();
            
            try {
                foreach ($newMembers as $memberEmail) {
                    // Vérifier si l'utilisateur n'est pas déjà membre
                    $existingMember = DB::table('personne_groupe')
                        ->where('email_personne', $memberEmail)
                        ->where('id_groupe_message', $groupId)
                        ->first();

                    if (!$existingMember) {
                        // Ajouter le nouveau membre
                        DB::table('personne_groupe')->insert([
                            'email_personne' => $memberEmail,
                            'id_groupe_message' => $groupId,
                            'date_adhesion' => now(),
                            'derniere_connexion' => null
                        ]);

                        // Récupérer les informations du membre ajouté
                        $memberInfo = Personne::where('email', $memberEmail)->first();
                        if ($memberInfo) {
                            $addedMembers[] = [
                                'email' => $memberInfo->email,
                                'nom' => $memberInfo->nom,
                                'prenom' => $memberInfo->prenom,
                                'nom_complet' => $memberInfo->nom_complet,
                                'role' => $this->getUserRole($memberInfo->email),
                                'is_current_user' => false,
                                'date_adhesion' => now()->toISOString()
                            ];
                        }
                    }
                }

                // Envoyer un message automatique pour informer de l'ajout
                if (!empty($addedMembers)) {
                    $membersNames = collect($addedMembers)->pluck('nom_complet')->join(', ');
                    $messageContenu = $user->nom_complet . " a ajouté " . $membersNames . " à la conversation.";
                    
                    Message::create([
                        'id_groupe_message' => $groupId,
                        'email_auteur' => $user->email,
                        'contenu_message' => $messageContenu,
                        'date_envoi' => now()
                    ]);
                }

                DB::commit();

                return response()->json([
                    'success' => true,
                    'message' => count($addedMembers) . ' membre(s) ajouté(s) avec succès',
                    'members' => $addedMembers
                ]);

            } catch (\Exception $e) {
                DB::rollBack();
                throw $e;
            }
            
        } catch (\Exception $e) {
            Log::error('Erreur lors de l\'ajout des membres: ' . $e->getMessage());
            
            return response()->json([
                'success' => false,
                'message' => 'Erreur lors de l\'ajout des membres',
                'error' => $e->getMessage()
            ], 500);
        }
    }

    // Méthode helper pour déterminer le rôle d'un utilisateur
    private function getUserRole($email)
    {
        if (DB::table('admin')->where('email_personne', $email)->exists()) {
            return 'Administrateur';
        } elseif (DB::table('gardien')->where('email_personne', $email)->exists()) {
            return 'Gardien';
        } elseif (DB::table('resident')->where('email_personne', $email)->exists()) {
            return 'Résident';
        }
        return 'Utilisateur';
    }
}

