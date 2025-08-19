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
        use \App\Traits\LoggableAction;

        /**
         * Supprimer une conversation (groupe) et tous ses messages (admin)
         */
        public function deleteConversation(Request $request, $groupId)
        {
            $user = $this->getCurrentUser($request);
            if (!$user) {
                return response()->json([
                    'success' => false,
                    'message' => 'Utilisateur non authentifié'
                ], 401);
            }
            $role = $this->getUserRole($user->id_personne);
            if ($role !== 'Administrateur') {
                return response()->json([
                    'success' => false,
                    'message' => 'Accès réservé aux administrateurs'
                ], 403);
            }
            $groupe = GroupeMessage::find($groupId);
            if (!$groupe) {
                return response()->json([
                    'success' => false,
                    'message' => 'Groupe non trouvé'
                ], 404);
            }
            \DB::beginTransaction();
            try {
                // Supprimer les fichiers liés aux messages
                $messages = Message::where('id_groupe_message', $groupId)->get();
                foreach ($messages as $msg) {
                    $fichiers = \DB::table('message_fichier')->where('id_message', $msg->id_message)->get();
                    foreach ($fichiers as $fichier) {
                        // Supprimer le fichier du stockage si besoin
                        if ($fichier->chemin_fichier) {
                            try {
                                \Storage::disk('public')->delete($fichier->chemin_fichier);
                            } catch (\Exception $e) {}
                        }
                    }
                    \DB::table('message_fichier')->where('id_message', $msg->id_message)->delete();
                    \DB::table('message_reaction')->where('id_message', $msg->id_message)->delete();
                }
                // Supprimer les messages
                Message::where('id_groupe_message', $groupId)->delete();
                // Supprimer les liens personne_groupe
                \DB::table('personne_groupe')->where('id_groupe_message', $groupId)->delete();
                // Supprimer le groupe
                $groupe->delete();
                \DB::commit();
                $this->logAction($user->id_personne, 'delete_conversation', "Suppression du groupe $groupId", $request);
                return response()->json([
                    'success' => true,
                    'message' => 'Conversation supprimée avec succès'
                ]);
            } catch (\Exception $e) {
                \DB::rollBack();
                return response()->json([
                    'success' => false,
                    'message' => 'Erreur lors de la suppression',
                    'error' => $e->getMessage()
                ], 500);
            }
        }
        /**
         * Récupérer l'utilisateur actuel (membre ou invité)
         */
        private function getCurrentUser(Request $request)
        {
            // Avec Sanctum, l'utilisateur est disponible directement
            $user = $request->user();
            
            if (!$user) {
                \Log::info('No authenticated user found');
                return null;
            }
            
            \Log::info('Authenticated user found:', [
                'email' => $user->email,
                'class' => get_class($user)
            ]);
            
            return $user;
        }

        // Récupérer les groupes de conversation de l'utilisateur connecté
        public function getConversations(Request $request)
        {
            try {
                $user = $this->getCurrentUser($request);
                
                if (!$user) {
                    return response()->json([
                        'success' => false,
                        'message' => 'Utilisateur non authentifié'
                    ], 401);
                }

                Log::info('Chargement des conversations pour l\'utilisateur ID: ' . $user->id_personne);
                
                // Récupérer les groupes avec le dernier message et le compteur de messages non lus
                $conversations = GroupeMessage::whereHas('personnes', function ($query) use ($user) {
                    $query->where('personne_groupe.id_personne', $user->id_personne);
                })
                ->with(['dernierMessage.auteur'])
                ->get()
                ->map(function ($groupe) use ($user) {
                    $dernierMessage = $groupe->dernierMessage;
                    
                    // Récupérer la dernière connexion de l'utilisateur pour ce groupe
                    $personneGroupe = DB::table('personne_groupe')
                        ->where('id_personne', $user->id_personne)
                        ->where('id_groupe_message', $groupe->id_groupe_message)
                        ->first();
                    
                    $derniereConnexion = $personneGroupe ? $personneGroupe->derniere_connexion : null;
                    
                    // Compter les messages non lus (envoyés après la dernière connexion)
                    $messagesNonLus = 0;
                    if ($derniereConnexion) {
                        $messagesNonLus = Message::where('id_groupe_message', $groupe->id_groupe_message)
                            ->where('date_envoi', '>', $derniereConnexion)
                            ->where('id_auteur', '!=', $user->id_personne) // Exclure ses propres messages
                            ->count();
                    } else {
                        // Si jamais connecté, tous les messages sont non lus (sauf les siens)
                        $messagesNonLus = Message::where('id_groupe_message', $groupe->id_groupe_message)
                            ->where('id_auteur', '!=', $user->id_personne)
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
        public function getMessages(Request $request, $groupId)
        {
            try {
                $user = $this->getCurrentUser($request);
                
                if (!$user) {
                    return response()->json([
                        'success' => false,
                        'message' => 'Utilisateur non authentifié'
                    ], 401);
                }

                // Vérifier que l'utilisateur fait partie du groupe
                $groupe = GroupeMessage::whereHas('personnes', function ($query) use ($user) {
                    $query->where('personne_groupe.id_personne', $user->id_personne);
                })->find($groupId);

                if (!$groupe) {
                    return response()->json([
                        'success' => false,
                        'message' => 'Accès non autorisé à ce groupe'
                    ], 403);
                }

                // Récupérer la dernière connexion de l'utilisateur pour ce groupe (une seule fois)
                $personneGroupe = DB::table('personne_groupe')
                    ->where('id_personne', $user->id_personne)
                    ->where('id_groupe_message', $groupId)
                    ->first();
                
                $derniereConnexion = $personneGroupe ? $personneGroupe->derniere_connexion : null;

                // Récupérer les messages avec réactions et fichiers
                $messages = DB::table('message')
                    ->join('personne', 'message.id_auteur', '=', 'personne.id_personne')
                    ->leftJoin('message_fichier', 'message.id_message', '=', 'message_fichier.id_message')
                    ->where('message.id_groupe_message', $groupId)
                    ->select(
                        'message.id_message',
                        'message.contenu_message',
                        'message.date_envoi',
                        'message.id_auteur',
                        'message.a_fichiers',
                        'message.reply_to',
                        'personne.nom',
                        'personne.prenom',
                        'personne.email',
                        DB::raw('CONCAT(personne.prenom, " ", personne.nom) as auteur_nom')
                    )
                    ->groupBy('message.id_message', 'message.contenu_message', 'message.date_envoi', 'message.id_auteur', 'message.a_fichiers', 'message.reply_to', 'personne.nom', 'personne.prenom', 'personne.email')
                    ->orderBy('message.date_envoi', 'asc')
                    ->get();

                $messagesArray = [];
                foreach ($messages as $message) {
                    // Récupérer les réactions pour ce message
                    $reactions = DB::table('message_reaction')
                        ->join('personne', 'message_reaction.id_personne', '=', 'personne.id_personne')
                        ->where('message_reaction.id_message', $message->id_message)
                        ->select(
                            'message_reaction.emoji',
                            'message_reaction.id_personne',
                            'personne.prenom',
                            'personne.nom',
                            'personne.email',
                            'message_reaction.date_reaction'
                        )
                        ->get()
                        ->groupBy('emoji')
                        ->map(function ($reactionGroup) {
                            return [
                                'count' => $reactionGroup->count(),
                                'users' => $reactionGroup->map(function ($reaction) {
                                    return [
                                        'id_personne' => $reaction->id_personne,
                                        'email' => $reaction->email,
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

                    // Déterminer le statut de lecture basé sur la dernière connexion
                    $statut = 'recu'; // Par défaut
                    if ($message->id_auteur === $user->id_personne) {
                        $statut = 'envoye'; // Message envoyé par l'utilisateur
                    } elseif ($derniereConnexion && $message->date_envoi <= $derniereConnexion) {
                        $statut = 'lu'; // Message lu (envoyé avant ou à la dernière connexion)
                    } else {
                        $statut = 'recu'; // Message non lu (envoyé après la dernière connexion ou jamais connecté)
                    }

                    // Construire l'éventuel bloc de citation
                    $replyPayload = null;
                    if ($message->reply_to) {
                        $replyRow = DB::table('message')
                            ->join('personne', 'message.id_auteur', '=', 'personne.id_personne')
                            ->where('message.id_message', $message->reply_to)
                            ->select('message.id_message','message.contenu_message','personne.prenom','personne.nom')
                            ->first();
                        if ($replyRow) {
                            $replyPayload = [
                                'id_message' => $replyRow->id_message,
                                'auteur_nom' => $replyRow->prenom . ' ' . $replyRow->nom,
                                'excerpt' => mb_substr($replyRow->contenu_message, 0, 120)
                            ];
                        }
                    }

                    $messagesArray[] = [
                        'id_message' => $message->id_message,
                        'contenu_message' => $message->contenu_message,
                        'date_envoi' => $message->date_envoi,
                        'id_auteur' => $message->id_auteur,
                        'email_auteur' => $message->email, // Pour compatibilité frontend
                        'auteur_nom' => $message->auteur_nom,
                        'is_current_user' => $message->id_auteur === $user->id_personne,
                        'reactions' => $reactions,
                        'fichiers' => $fichiers,
                        'statut_lecture' => $statut,
                        'a_fichiers' => (bool)$message->a_fichiers,
                        'reply_to' => $replyPayload
                    ];
                }

                // Marquer tous les messages comme lus pour cet utilisateur
                $this->markMessagesAsRead($groupId, $user->id_personne);
                $this->logAction($user->id_personne, 'read_messages', "Messages marqués comme lus dans le groupe $groupId", $request);

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
        public function markAsRead(Request $request, $groupId)
        {
            try {
                $user = $this->getCurrentUser($request);
                
                if (!$user) {
                    return response()->json([
                        'success' => false,
                        'message' => 'Utilisateur non authentifié'
                    ], 401);
                }

                // Vérifier que l'utilisateur fait partie du groupe
                $groupe = GroupeMessage::whereHas('personnes', function ($query) use ($user) {
                    $query->where('personne_groupe.id_personne', $user->id_personne);
                })->find($groupId);

                if (!$groupe) {
                    return response()->json([
                        'success' => false,
                        'message' => 'Accès non autorisé à ce groupe'
                    ], 403);
                }

                // Mettre à jour la dernière connexion
                DB::table('personne_groupe')
                    ->where('id_personne', $user->id_personne)
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
                $user = $this->getCurrentUser($request);
                
                if (!$user) {
                    return response()->json([
                        'success' => false,
                        'message' => 'Utilisateur non authentifié'
                    ], 401);
                }

                $request->validate([
                    'contenu' => 'nullable|string|max:5000',
                    'fichiers' => 'nullable|array|max:5',
                    'fichiers.*' => 'file|max:10240', // 10MB max par fichier
                    'reply_to' => 'nullable|integer'
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
                    $query->where('personne_groupe.id_personne', $user->id_personne);
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
                    $replyMessage = null;
                    if ($request->reply_to) {
                        $replyMessage = DB::table('message')
                            ->where('id_message', $request->reply_to)
                            ->where('id_groupe_message', $groupId)
                            ->first();
                    }

                    $message = Message::create([
                        'id_groupe_message' => $groupId,
                        'id_auteur' => $user->id_personne,
                        'contenu_message' => $request->contenu ?? '',
                        'date_envoi' => now(),
                        'a_fichiers' => $request->hasFile('fichiers'),
                        'reply_to' => $replyMessage ? $request->reply_to : null
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

                    DB::commit();
                    $this->logAction($user->id_personne, 'send_message', "Message envoyé dans le groupe $groupId", $request);

                    $replyPayload = null;
                    if ($replyMessage) {
                        $replyAuthor = DB::table('personne')
                            ->where('id_personne', $replyMessage->id_auteur)
                            ->select('prenom', 'nom')
                            ->first();
                        $replyPayload = [
                            'id_message' => $replyMessage->id_message,
                            'auteur_nom' => $replyAuthor ? ($replyAuthor->prenom . ' ' . $replyAuthor->nom) : '',
                            'excerpt' => mb_substr($replyMessage->contenu_message, 0, 120)
                        ];
                    }

                    return response()->json([
                        'success' => true,
                        'message' => [
                            'id_message' => $message->id_message,
                            'contenu_message' => $message->contenu_message,
                            'date_envoi' => $message->date_envoi->toISOString(),
                            'email_auteur' => $user->email,
                            'auteur_nom' => $user->nom_complet,
                            'is_current_user' => true,
                            'reactions' => [],
                            'fichiers' => $fichiers,
                            'statut_lecture' => 'envoye',
                            'a_fichiers' => !empty($fichiers),
                            'reply_to' => $replyPayload
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
                $user = $this->getCurrentUser($request);
                
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
                    ->where('personne_groupe.id_personne', $user->id_personne)
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
                    ->where('id_personne', $user->id_personne)
                    ->where('emoji', $request->emoji)
                    ->first();

                if ($existingReaction) {
                    // Supprimer la réaction si elle existe déjà
                    DB::table('message_reaction')
                        ->where('id_reaction', $existingReaction->id_reaction)
                        ->delete();
                    $action = 'removed';
                    $this->logAction($user->id_personne, 'remove_reaction', "Réaction supprimée sur le message $messageId", $request);
                } else {
                    // Ajouter la réaction
                    DB::table('message_reaction')->insert([
                        'id_message' => $messageId,
                        'id_personne' => $user->id_personne,
                        'emoji' => $request->emoji,
                        'date_reaction' => now()
                    ]);
                    $action = 'added';
                    $this->logAction($user->id_personne, 'add_reaction', "Réaction ajoutée sur le message $messageId", $request);
                }

                // Récupérer les réactions mises à jour
                $reactions = DB::table('message_reaction')
                    ->join('personne', 'message_reaction.id_personne', '=', 'personne.id_personne')
                    ->where('message_reaction.id_message', $messageId)
                    ->select(
                        'message_reaction.emoji',
                        'message_reaction.id_personne',
                        'personne.prenom',
                        'personne.nom',
                        'personne.email'
                    )
                    ->get()
                    ->groupBy('emoji')
                    ->map(function ($reactionGroup) {
                        return [
                            'count' => $reactionGroup->count(),
                            'users' => $reactionGroup->map(function ($reaction) {
                                return [
                                    'email' => $reaction->email,
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
        public function downloadFile(Request $request, $fichierId)
        {
            try {
                $user = $this->getCurrentUser($request);
                
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
                    ->where('personne_groupe.id_personne', $user->id_personne)
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

                // Si le paramètre 'inline' est présent et que c'est une image, l'afficher en ligne
                if ($request->has('inline') && $this->isImageFile($fichier->type_fichier)) {
                    return response()->file($cheminComplet, [
                        'Content-Type' => $fichier->type_fichier,
                        'Cache-Control' => 'public, max-age=31536000', // Cache d'un an
                    ]);
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

        // Vérifier si un fichier est une image
        private function isImageFile($mimeType)
        {
            $imageTypes = [
                'image/jpeg',
                'image/jpg', 
                'image/png',
                'image/gif',
                'image/webp',
                'image/svg+xml',
                'image/bmp'
            ];
            
            return in_array(strtolower($mimeType), $imageTypes);
        }

        // Marquer les messages comme lus en mettant à jour la dernière connexion
        private function markMessagesAsRead($groupId, $userIdPersonne)
        {
            // Mettre à jour la dernière connexion de l'utilisateur pour ce groupe
            // Cela marquera automatiquement tous les messages précédents comme lus
            DB::table('personne_groupe')
                ->where('id_personne', $userIdPersonne)
                ->where('id_groupe_message', $groupId)
                ->update(['derniere_connexion' => now()]);
        }

        // Créer une nouvelle conversation
        public function createConversation(Request $request)
        {
            try {
                $user = $this->getCurrentUser($request);
                if (!$user) {
                    return response()->json([
                        'success' => false,
                        'message' => 'Utilisateur non authentifié'
                    ], 401);
                }

                // Log brut du contenu reçu (pour diagnostiquer perte de payload via proxy)
                Log::info('Payload brut création conversation', [
                    'raw' => $request->getContent(),
                    'parsed_all' => $request->all()
                ]);

                // Parfois via proxy certains champs peuvent être perdus: tentative de récupération manuelle si manquants
                $rawJson = [];
                if ($request->getContent()) {
                    try { $rawJson = json_decode($request->getContent(), true) ?: []; } catch (\Throwable $t) { $rawJson = []; }
                }

                // Fusion prudente: ne pas écraser les valeurs déjà présentes
                foreach (['nom_groupe','participants'] as $k) {
                    if (!$request->has($k) && array_key_exists($k, $rawJson)) {
                        // Injecter dans la request pour validation
                        $request->merge([$k => $rawJson[$k]]);
                    }
                }

                try {
                    $validated = $request->validate([
                        'nom_groupe' => 'required|string|max:100',
                        'participants' => 'required|array|min:1',
                        'participants.*' => 'required|email|exists:personne,email'
                    ]);
                } catch (\Illuminate\Validation\ValidationException $ve) {
                    Log::warning('Validation création conversation échouée', [
                        'errors' => $ve->errors(),
                        'input' => $request->all()
                    ]);
                    return response()->json([
                        'success' => false,
                        'message' => 'Validation échouée',
                        'errors' => $ve->errors()
                    ], 422);
                }

                Log::info('Création d\'une nouvelle conversation par ID: ' . $user->id_personne);

                // Normaliser la liste des participants (collection)
                $participants = collect($validated['participants'] ?? []);
                if (!$participants->contains($user->email)) {
                    $participants->push($user->email);
                }
                $participants = $participants->unique();

                // Convertir en IDs
                $participantIds = Personne::whereIn('email', $participants)->pluck('id_personne')->all();

                DB::beginTransaction();
                try {
                    $groupe = GroupeMessage::create([
                        'nom_groupe' => $validated['nom_groupe'],
                        'date_creation' => now()
                    ]);

                    foreach ($participantIds as $participantId) {
                        DB::table('personne_groupe')->insert([
                            'id_personne' => $participantId,
                            'id_groupe_message' => $groupe->id_groupe_message,
                            'date_adhesion' => now(),
                            'derniere_connexion' => $participantId === $user->id_personne ? now() : null
                        ]);
                    }

                    $messageContenu = 'Conversation créée par ' . $user->nom_complet . '. Bienvenue à tous !';
                    Message::create([
                        'id_groupe_message' => $groupe->id_groupe_message,
                        'id_auteur' => $user->id_personne,
                        'contenu_message' => $messageContenu,
                        'date_envoi' => now()
                    ]);

                    DB::commit();
                    $this->logAction($user->id_personne, 'create_conversation', "Création d'une conversation: $groupe->nom_groupe", $request);
                    Log::info('Conversation créée avec succès: ' . $groupe->id_groupe_message);

                    $conversationData = [
                        'id_groupe_message' => $groupe->id_groupe_message,
                        'nom_groupe' => $groupe->nom_groupe,
                        'date_creation' => $groupe->date_creation->toISOString(),
                        'derniere_activite' => $groupe->date_creation->toISOString(),
                        'dernier_contenu' => $messageContenu,
                        'dernier_auteur' => $user->nom_complet,
                        'nombre_membres' => count($participantIds),
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
        public function getAvailableUsers(Request $request)
        {
            try {
                $user = $this->getCurrentUser($request);
                
                if (!$user) {
                    return response()->json([
                        'success' => false,
                        'message' => 'Utilisateur non authentifié'
                    ], 401);
                }

                // Récupérer tous les utilisateurs sauf l'utilisateur actuel
                $users = Personne::where('email', '!=', $user->email)
                    ->select('id_personne','email', 'nom', 'prenom')
                    ->get()
                    ->map(function ($personne) {
                        return [
                            'email' => $personne->email,
                            'nom_complet' => $personne->nom_complet,
                            'nom' => $personne->nom,
                            'prenom' => $personne->prenom,
                            'role' => $this->getUserRole($personne->id_personne)
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
        public function getGroupMembers(Request $request, $groupId)
        {
            try {
                $user = $this->getCurrentUser($request);
                
                if (!$user) {
                    return response()->json([
                        'success' => false,
                        'message' => 'Utilisateur non authentifié'
                    ], 401);
                }

                // Vérifier que l'utilisateur fait partie du groupe
                $groupe = GroupeMessage::whereHas('personnes', function ($query) use ($user) {
                    $query->where('personne_groupe.id_personne', $user->id_personne);
                })->find($groupId);

                if (!$groupe) {
                    return response()->json([
                        'success' => false,
                        'message' => 'Accès non autorisé à ce groupe'
                    ], 403);
                }

                // Récupérer les membres du groupe
                $members = DB::table('personne_groupe')
                    ->join('personne', 'personne_groupe.id_personne', '=', 'personne.id_personne')
                    ->where('personne_groupe.id_groupe_message', $groupId)
                    ->select(
                        'personne.id_personne',
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
                            'role' => $this->getUserRole($member->id_personne),
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
                $user = $this->getCurrentUser($request);
                
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
                    $query->where('personne_groupe.id_personne', $user->id_personne);
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
                        // Récupérer les informations du membre par email
                        $memberInfo = Personne::where('email', $memberEmail)->first();
                        if (!$memberInfo) {
                            continue; // Passer au suivant si l'utilisateur n'existe pas
                        }
                        
                        // Vérifier si l'utilisateur n'est pas déjà membre
                        $existingMember = DB::table('personne_groupe')
                            ->where('id_personne', $memberInfo->id_personne)
                            ->where('id_groupe_message', $groupId)
                            ->first();

                        if (!$existingMember) {
                            // Ajouter le nouveau membre
                            DB::table('personne_groupe')->insert([
                                'id_personne' => $memberInfo->id_personne,
                                'id_groupe_message' => $groupId,
                                'date_adhesion' => now(),
                                'derniere_connexion' => null
                            ]);
                            
                            $addedMembers[] = [
                                'email' => $memberInfo->email,
                                'nom' => $memberInfo->nom,
                                'prenom' => $memberInfo->prenom,
                                'nom_complet' => $memberInfo->nom_complet,
                                'role' => $this->getUserRole($memberInfo->id_personne),
                                'is_current_user' => false,
                                'date_adhesion' => now()->toISOString()
                            ];
                        }
                    }

                    // Envoyer un message automatique pour informer de l'ajout
                    if (!empty($addedMembers)) {
                        $membersNames = collect($addedMembers)->pluck('nom_complet')->join(', ');
                        $messageContenu = $user->nom_complet . " a ajouté " . $membersNames . " à la conversation.";
                        
                        Message::create([
                            'id_groupe_message' => $groupId,
                            'id_auteur' => $user->id_personne,
                            'contenu_message' => $messageContenu,
                            'date_envoi' => now()
                        ]);
                    }

                    DB::commit();
                    $this->logAction($user->id_personne, 'add_group_members', "Ajout de membres au groupe $groupId", $request);

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

        /**
         * Vérifier s'il y a des changements dans les conversations (endpoint léger)
         */
        public function checkConversationsChanges(Request $request)
        {
            try {
                $user = $this->getCurrentUser($request);
                if (!$user) {
                    return response()->json(['success' => false, 'error' => 'Non authentifié'], 401);
                }

                // Récupérer le hash des conversations avec leur dernière activité
                $conversationsHash = DB::table('personne_groupe')
                    ->join('groupe_message', 'personne_groupe.id_groupe_message', '=', 'groupe_message.id_groupe_message')
                    ->leftJoin('message', function($join) {
                        $join->on('groupe_message.id_groupe_message', '=', 'message.id_groupe_message')
                            ->whereRaw('message.date_envoi = (
                                SELECT MAX(m2.date_envoi) 
                                FROM message m2 
                                WHERE m2.id_groupe_message = groupe_message.id_groupe_message
                            )');
                    })
                    ->where('personne_groupe.id_personne', $user->id_personne)
                    ->select(
                        'groupe_message.id_groupe_message',
                        DB::raw('COALESCE(MAX(message.date_envoi), groupe_message.date_creation) as derniere_activite')
                    )
                    ->groupBy('groupe_message.id_groupe_message', 'groupe_message.date_creation')
                    ->orderBy('derniere_activite', 'desc')
                    ->get();

                // Créer un hash simple basé sur les IDs et timestamps
                $hashString = $conversationsHash->map(function($conv) {
                    return $conv->id_groupe_message . ':' . $conv->derniere_activite;
                })->implode('|');
                
                $hash = md5($hashString);

                return response()->json([
                    'success' => true,
                    'hash' => $hash,
                    'count' => $conversationsHash->count(),
                    'last_activity' => $conversationsHash->first()->derniere_activite ?? null
                ]);

            } catch (\Exception $e) {
                Log::error('Erreur check conversations changes: ' . $e->getMessage());
                return response()->json(['success' => false, 'error' => 'Erreur serveur'], 500);
            }
        }

        /**
         * Vérifier s'il y a des changements dans les messages d'une conversation (endpoint léger)
         */
        public function checkMessagesChanges(Request $request, $groupId)
        {
            try {
                $user = $this->getCurrentUser($request);
                if (!$user) {
                    return response()->json(['success' => false, 'error' => 'Non authentifié'], 401);
                }

                // Vérifier l'accès au groupe
                $hasAccess = DB::table('personne_groupe')
                    ->where('id_personne', $user->id_personne)
                    ->where('id_groupe_message', $groupId)
                    ->exists();

                if (!$hasAccess) {
                    return response()->json(['success' => false, 'error' => 'Accès refusé'], 403);
                }

                // Récupérer les infos de base des messages (sans le contenu)
                $messagesInfo = DB::table('message')
                    ->where('id_groupe_message', $groupId)
                    ->select('id_message', 'date_envoi')
                    ->orderBy('date_envoi', 'asc')
                    ->get();

                // Créer un hash basé sur les IDs et timestamps des messages
                $hashString = $messagesInfo->map(function($msg) {
                    return $msg->id_message . ':' . $msg->date_envoi;
                })->implode('|');
                
                $hash = md5($hashString);

                // Compter les messages non lus pour cet utilisateur
                $derniereConnexion = DB::table('personne_groupe')
                    ->where('id_personne', $user->id_personne)
                    ->where('id_groupe_message', $groupId)
                    ->value('derniere_connexion');

                $messagesNonLus = 0;
                if ($derniereConnexion) {
                    $messagesNonLus = DB::table('message')
                        ->where('id_groupe_message', $groupId)
                        ->where('id_auteur', '!=', $user->id_personne)
                        ->where('date_envoi', '>', $derniereConnexion)
                        ->count();
                } else {
                    // Si pas de dernière connexion, tous les messages des autres sont non lus
                    $messagesNonLus = DB::table('message')
                        ->where('id_groupe_message', $groupId)
                        ->where('id_auteur', '!=', $user->id_personne)
                        ->count();
                }

                return response()->json([
                    'success' => true,
                    'hash' => $hash,
                    'count' => $messagesInfo->count(),
                    'unread_count' => $messagesNonLus,
                    'last_message_date' => $messagesInfo->last()->date_envoi ?? null
                ]);

            } catch (\Exception $e) {
                Log::error('Erreur check messages changes: ' . $e->getMessage());
                return response()->json(['success' => false, 'error' => 'Erreur serveur'], 500);
            }
        }

        // Méthode helper pour déterminer le rôle d'un utilisateur
        private function getUserRole($id_personne)
        {
            if (DB::table('admin')->where('id_personne', $id_personne)->exists()) {
                return 'Administrateur';
            } elseif (DB::table('gardien')->where('id_personne', $id_personne)->exists()) {
                return 'Gardien';
            } elseif (DB::table('resident')->where('id_personne', $id_personne)->exists()) {
                return 'Résident';
            }
            return 'Utilisateur';
        }

    /**
     * Récupérer tous les messages pour la modération admin
     * Filtres : personne, groupe, contenu, tri chronologique
     */
    public function getAllMessages(Request $request)
    {
        $user = $this->getCurrentUser($request);
        if (!$user) {
            return response()->json([
                'success' => false,
                'message' => 'Utilisateur non authentifié'
            ], 401);
        }
        // Vérifier que l'utilisateur est admin
        $role = $this->getUserRole($user->id_personne);
        if ($role !== 'Administrateur') {
            return response()->json([
                'success' => false,
                'message' => 'Accès réservé aux administrateurs'
            ], 403);
        }

        // Filtres
        $personId = $request->query('personId');
        $groupId = $request->query('groupId');
        $search = $request->query('search');
        $sort = $request->query('sort', 'desc'); // 'asc' ou 'desc'

        $query = DB::table('message')
            ->join('personne', 'message.id_auteur', '=', 'personne.id_personne')
            ->join('groupe_message', 'message.id_groupe_message', '=', 'groupe_message.id_groupe_message')
            ->select(
                'message.id_message',
                'message.contenu_message',
                'message.date_envoi',
                'message.id_auteur',
                'personne.nom as auteur_nom',
                'personne.email as auteur_email',
                'groupe_message.nom_groupe',
                'groupe_message.id_groupe_message'
            );

        if ($personId) {
            $query->where('message.id_auteur', $personId);
        }
        if ($groupId) {
            $query->where('message.id_groupe_message', $groupId);
        }
        if ($search) {
            $query->where('message.contenu_message', 'like', "%$search%");
        }

        $messages = $query->orderBy('message.date_envoi', $sort)->limit(500)->get();

        return response()->json([
            'success' => true,
            'messages' => $messages
        ]);
    }
    }
?>


