<?php

    namespace App\Http\Controllers\Api;

    use App\Http\Controllers\Controller;
    use App\Models\Badge;
    use App\Models\SuiviBadge;
    use App\Models\Personne;
    use Illuminate\Http\Request;
    use Illuminate\Support\Facades\Auth;
    use Illuminate\Support\Facades\DB;
    use Illuminate\Support\Facades\Validator;

    class BadgeController extends Controller
    {
        /**
         * Lister tous les badges avec informations utilisateur
         */
        public function index(Request $request)
        {
            try {
                $query = Badge::with(['utilisateur', 'suivis' => function($q) {
                    $q->orderBy('date_heure', 'desc')->limit(1);
                }]);

                // Filtres optionnels
                if ($request->has('search') && $request->search) {
                    $search = $request->search;
                    $query->where(function($q) use ($search) {
                        $q->whereHas('utilisateur', function($subq) use ($search) {
                            $subq->where('nom', 'LIKE', "%{$search}%")
                                ->orWhere('prenom', 'LIKE', "%{$search}%")
                                ->orWhere('email', 'LIKE', "%{$search}%");
                        })->orWhere('numero', 'LIKE', "%{$search}%")
                        ->orWhere('commentaire', 'LIKE', "%{$search}%")
                        ->orWhere('droit', 'LIKE', "%{$search}%")
                        ->orWhere('zone_acces', 'LIKE', "%{$search}%")
                        ->orWhere('niveau_securite', 'LIKE', "%{$search}%");
                    });
                }

                if ($request->has('statut') && $request->statut) {
                    $statut = $request->statut;
                    
                    // Utiliser d'abord la colonne statut si elle existe
                    $query->where(function($q) use ($statut) {
                        $q->where('statut', $statut)
                        ->orWhere(function($subq) use ($statut) {
                            // Fallback sur l'ancien système avec suivi_badge
                            if ($statut === 'actif') {
                                $subq->whereExists(function($existsQuery) {
                                    $existsQuery->select(DB::raw(1))
                                        ->from('suivi_badge')
                                        ->whereRaw('suivi_badge.id_badge = badge.numero')
                                        ->whereIn('action', ['activation', 'utilisation', 'reactivation'])
                                        ->orderBy('date_heure', 'desc')
                                        ->limit(1);
                                });
                            } elseif ($statut === 'inactif') {
                                $subq->whereExists(function($existsQuery) {
                                    $existsQuery->select(DB::raw(1))
                                        ->from('suivi_badge')
                                        ->whereRaw('suivi_badge.id_badge = badge.numero')
                                        ->whereIn('action', ['desactivation', 'désactivation'])
                                        ->orderBy('date_heure', 'desc')
                                        ->limit(1);
                                });
                            } elseif ($statut === 'suspendu') {
                                $subq->whereExists(function($existsQuery) {
                                    $existsQuery->select(DB::raw(1))
                                        ->from('suivi_badge')
                                        ->whereRaw('suivi_badge.id_badge = badge.numero')
                                        ->where('action', 'suspension')
                                        ->orderBy('date_heure', 'desc')
                                        ->limit(1);
                                });
                            }
                        });
                    });
                }

                $badges = $query->orderBy('numero', 'desc')->get();

                // Enrichir les données
                $badgesData = $badges->map(function($badge) {
                    return [
                        'numero' => $badge->numero,
                        'id_utilisateur' => $badge->id_utilisateur,
                        'utilisateur_id' => $badge->id_utilisateur, // Alias pour compatibilité frontend
                        'commentaire' => $badge->commentaire,
                        'droit' => $badge->droit,
                        'statut' => $badge->statut,
                        'zone_acces' => $badge->zone_acces,
                        'niveau_securite' => $badge->niveau_securite,
                        'date_creation' => $badge->date_creation,
                        'date_derniere_utilisation' => $badge->date_derniere_utilisation,
                        'utilisateur' => $badge->utilisateur ? [
                            'id' => $badge->utilisateur->id_personne,
                            'nom_complet' => $badge->utilisateur->nom_complet,
                            'email' => $badge->utilisateur->email,
                            'telephone' => $badge->utilisateur->numero_telephone,
                        ] : null,
                        'statut_actuel' => $badge->statut_actuel,
                        'derniere_utilisation' => $badge->derniere_utilisation,
                        'dernier_suivi' => $badge->suivis->first() ? [
                            'date_heure' => $badge->suivis->first()->date_heure,
                            'action' => $badge->suivis->first()->action,
                            'action_libelle' => $badge->suivis->first()->action_libelle,
                            'action_color' => $badge->suivis->first()->action_color,
                            'message' => $badge->suivis->first()->message,
                        ] : null
                    ];
                });

                return response()->json([
                    'success' => true,
                    'data' => $badgesData,
                    'total' => $badges->count()
                ]);

            } catch (\Exception $e) {
                return response()->json([
                    'success' => false,
                    'message' => 'Erreur lors de la récupération des badges',
                    'error' => $e->getMessage()
                ], 500);
            }
        }

        /**
         * Créer un nouveau badge
         */
        public function store(Request $request)
        {
            try {
                $validator = Validator::make($request->all(), [
                    'id_utilisateur' => 'required|integer|exists:personne,id_personne',
                    'commentaire' => 'nullable|string|max:512',
                    'droit' => 'required|string|max:255',
                ]);

                if ($validator->fails()) {
                    return response()->json([
                        'success' => false,
                        'message' => 'Données invalides',
                        'errors' => $validator->errors()
                    ], 422);
                }

                DB::beginTransaction();

                // Créer le badge
                $badge = Badge::create([
                    'id_utilisateur' => $request->id_utilisateur,
                    'commentaire' => $request->commentaire,
                    'droit' => $request->droit,
                ]);

                // Créer un suivi pour la création
                SuiviBadge::create([
                    'date_heure' => now(),
                    'id_badge' => $badge->numero,
                    'action' => 'création',
                    'message' => 'Badge créé par ' . (Auth::user()->nom_complet ?? 'Système')
                ]);

                // Activer automatiquement le badge
                SuiviBadge::create([
                    'date_heure' => now(),
                    'id_badge' => $badge->numero,
                    'action' => 'activation',
                    'message' => 'Badge activé automatiquement à la création'
                ]);

                DB::commit();

                // Recharger avec les relations
                $badge->load(['utilisateur', 'suivis']);

                return response()->json([
                    'success' => true,
                    'message' => 'Badge créé avec succès',
                    'data' => [
                        'numero' => $badge->numero,
                        'id_utilisateur' => $badge->id_utilisateur,
                        'commentaire' => $badge->commentaire,
                        'droit' => $badge->droit,
                        'utilisateur' => [
                            'id' => $badge->utilisateur->id_personne,
                            'nom_complet' => $badge->utilisateur->nom_complet,
                            'email' => $badge->utilisateur->email,
                        ],
                        'statut_actuel' => 'actif'
                    ]
                ], 201);

            } catch (\Exception $e) {
                DB::rollBack();
                return response()->json([
                    'success' => false,
                    'message' => 'Erreur lors de la création du badge',
                    'error' => $e->getMessage()
                ], 500);
            }
        }

        /**
         * Afficher un badge spécifique
         */
        public function show($numero)
        {
            try {
                $badge = Badge::with(['utilisateur', 'suivis'])->find($numero);

                if (!$badge) {
                    return response()->json([
                        'success' => false,
                        'message' => 'Badge non trouvé'
                    ], 404);
                }

                return response()->json([
                    'success' => true,
                    'data' => [
                        'numero' => $badge->numero,
                        'id_utilisateur' => $badge->id_utilisateur,
                        'commentaire' => $badge->commentaire,
                        'droit' => $badge->droit,
                        'utilisateur' => [
                            'id' => $badge->utilisateur->id_personne ?? null,
                            'nom_complet' => $badge->utilisateur ? $badge->utilisateur->nom_complet : 'Utilisateur supprimé',
                            'email' => $badge->utilisateur->email ?? null,
                            'telephone' => $badge->utilisateur->numero_telephone ?? null,
                        ],
                        'statut_actuel' => $badge->statut_actuel,
                        'derniere_utilisation' => $badge->derniere_utilisation,
                        'suivis' => $badge->suivis->map(function($suivi) {
                            return [
                                'id' => $suivi->id,
                                'date_heure' => $suivi->date_heure,
                                'action' => $suivi->action,
                                'action_libelle' => $suivi->action_libelle,
                                'action_color' => $suivi->action_color,
                                'message' => $suivi->message,
                            ];
                        })
                    ]
                ]);

            } catch (\Exception $e) {
                return response()->json([
                    'success' => false,
                    'message' => 'Erreur lors de la récupération du badge',
                    'error' => $e->getMessage()
                ], 500);
            }
        }

        /**
         * Mettre à jour un badge
         */
        public function update(Request $request, $numero)
        {
            try {
                $badge = Badge::find($numero);

                if (!$badge) {
                    return response()->json([
                        'success' => false,
                        'message' => 'Badge non trouvé'
                    ], 404);
                }

                $validator = Validator::make($request->all(), [
                    'id_utilisateur' => 'sometimes|integer|exists:personne,id_personne',
                    'commentaire' => 'nullable|string|max:512',
                    'droit' => 'sometimes|string|max:255',
                ]);

                if ($validator->fails()) {
                    return response()->json([
                        'success' => false,
                        'message' => 'Données invalides',
                        'errors' => $validator->errors()
                    ], 422);
                }

                DB::beginTransaction();

                $anciennesValeurs = [
                    'id_utilisateur' => $badge->id_utilisateur,
                    'commentaire' => $badge->commentaire,
                    'droit' => $badge->droit,
                ];

                // Mettre à jour le badge
                $badge->update($request->only(['id_utilisateur', 'commentaire', 'droit']));

                // Créer un suivi de modification
                $modifications = [];
                foreach ($request->only(['id_utilisateur', 'commentaire', 'droit']) as $key => $value) {
                    if (isset($anciennesValeurs[$key]) && $anciennesValeurs[$key] != $value) {
                        $modifications[] = "{$key}: '{$anciennesValeurs[$key]}' → '{$value}'";
                    }
                }

                if (!empty($modifications)) {
                    SuiviBadge::create([
                        'date_heure' => now(),
                        'id_badge' => $badge->numero,
                        'action' => 'modification',
                        'message' => 'Modifié par ' . (Auth::user()->nom_complet ?? 'Système') . '. Changements: ' . implode(', ', $modifications)
                    ]);
                }

                DB::commit();

                // Recharger avec les relations
                $badge->load(['utilisateur', 'suivis']);

                return response()->json([
                    'success' => true,
                    'message' => 'Badge mis à jour avec succès',
                    'data' => [
                        'numero' => $badge->numero,
                        'id_utilisateur' => $badge->id_utilisateur,
                        'commentaire' => $badge->commentaire,
                        'droit' => $badge->droit,
                        'utilisateur' => [
                            'id' => $badge->utilisateur->id_personne ?? null,
                            'nom_complet' => $badge->utilisateur ? $badge->utilisateur->nom_complet : 'Utilisateur supprimé',
                            'email' => $badge->utilisateur->email ?? null,
                        ],
                        'statut_actuel' => $badge->statut_actuel
                    ]
                ]);

            } catch (\Exception $e) {
                DB::rollBack();
                return response()->json([
                    'success' => false,
                    'message' => 'Erreur lors de la mise à jour du badge',
                    'error' => $e->getMessage()
                ], 500);
            }
        }

        /**
         * Supprimer un badge
         */
        public function destroy($numero)
        {
            try {
                $badge = Badge::find($numero);

                if (!$badge) {
                    return response()->json([
                        'success' => false,
                        'message' => 'Badge non trouvé'
                    ], 404);
                }

                DB::beginTransaction();

                // Créer un suivi de suppression avant de supprimer
                SuiviBadge::create([
                    'date_heure' => now(),
                    'id_badge' => $badge->numero,
                    'action' => 'suppression',
                    'message' => 'Badge supprimé par ' . (Auth::user()->nom_complet ?? 'Système')
                ]);

                // Supprimer le badge (les suivis seront supprimés en cascade)
                $badge->delete();

                DB::commit();

                return response()->json([
                    'success' => true,
                    'message' => 'Badge supprimé avec succès'
                ]);

            } catch (\Exception $e) {
                DB::rollBack();
                return response()->json([
                    'success' => false,
                    'message' => 'Erreur lors de la suppression du badge',
                    'error' => $e->getMessage()
                ], 500);
            }
        }

        /**
         * Activer/Désactiver un badge
         */
        public function toggleStatus(Request $request, $numero)
        {
            try {
                $badge = Badge::find($numero);

                if (!$badge) {
                    return response()->json([
                        'success' => false,
                        'message' => 'Badge non trouvé'
                    ], 404);
                }

                $statutActuel = $badge->statut_actuel;
                $nouvelleAction = $statutActuel === 'actif' ? 'désactivation' : 'activation';
                
                $message = $request->message ?? ($nouvelleAction === 'activation' ? 'Badge activé manuellement' : 'Badge désactivé manuellement');

                // Créer un suivi pour le changement de statut
                SuiviBadge::create([
                    'date_heure' => now(),
                    'id_badge' => $badge->numero,
                    'action' => $nouvelleAction,
                    'message' => $message . ' par ' . (Auth::user()->nom_complet ?? 'Système')
                ]);

                // Recharger le badge pour obtenir le nouveau statut
                $badge->load(['suivis']);

                return response()->json([
                    'success' => true,
                    'message' => 'Statut du badge mis à jour avec succès',
                    'data' => [
                        'numero' => $badge->numero,
                        'ancien_statut' => $statutActuel,
                        'nouveau_statut' => $badge->statut_actuel
                    ]
                ]);

            } catch (\Exception $e) {
                return response()->json([
                    'success' => false,
                    'message' => 'Erreur lors du changement de statut',
                    'error' => $e->getMessage()
                ], 500);
            }
        }

        /**
         * Rechercher des utilisateurs pour attribution de badge
         */
        public function searchUsers(Request $request)
        {
            try {
                $search = $request->get('search', '');
                
                $users = Personne::where(function($query) use ($search) {
                    $query->where('nom', 'LIKE', "%{$search}%")
                        ->orWhere('prenom', 'LIKE', "%{$search}%")
                        ->orWhere('email', 'LIKE', "%{$search}%");
                })
                ->limit(10)
                ->get()
                ->map(function($user) {
                    return [
                        'id' => $user->id_personne,
                        'nom_complet' => $user->nom_complet,
                        'email' => $user->email,
                        'telephone' => $user->numero_telephone,
                        'nb_badges' => $user->badges()->count()
                    ];
                });

                return response()->json([
                    'success' => true,
                    'data' => $users
                ]);

            } catch (\Exception $e) {
                return response()->json([
                    'success' => false,
                    'message' => 'Erreur lors de la recherche d\'utilisateurs',
                    'error' => $e->getMessage()
                ], 500);
            }
        }

        /**
         * Obtenir les statistiques des badges
         */
        public function stats()
        {
            try {
                $totalBadges = Badge::count();
                
                // Badges actifs : dernier statut = activation ou utilisation
                $badgesActifs = Badge::whereExists(function($q) {
                    $q->select(DB::raw(1))
                    ->from('suivi_badge')
                    ->whereRaw('suivi_badge.id_badge = badge.numero')
                    ->whereIn('action', ['activation', 'utilisation'])
                    ->whereNotExists(function($subq) {
                        $subq->select(DB::raw(1))
                            ->from('suivi_badge as sb2')
                            ->whereRaw('sb2.id_badge = suivi_badge.id_badge')
                            ->whereIn('sb2.action', ['désactivation', 'suspension'])
                            ->whereRaw('sb2.date_heure > suivi_badge.date_heure');
                    });
                })->count();
                
                // Badges suspendus : dernier statut = suspension
                $badgesSuspendus = Badge::whereExists(function($q) {
                    $q->select(DB::raw(1))
                    ->from('suivi_badge')
                    ->whereRaw('suivi_badge.id_badge = badge.numero')
                    ->where('action', 'suspension')
                    ->whereNotExists(function($subq) {
                        $subq->select(DB::raw(1))
                            ->from('suivi_badge as sb2')
                            ->whereRaw('sb2.id_badge = suivi_badge.id_badge')
                            ->whereRaw('sb2.date_heure > suivi_badge.date_heure');
                    });
                })->count();
                
                // Badges inactifs : dernier statut = désactivation ou pas de statut
                $badgesInactifs = $totalBadges - $badgesActifs - $badgesSuspendus;
                
                $utilisationsAujourdhui = SuiviBadge::where('action', 'utilisation')
                    ->whereDate('date_heure', today())
                    ->count();

                $dernieresActivites = SuiviBadge::with(['badge.utilisateur'])
                    ->orderBy('date_heure', 'desc')
                    ->limit(10)
                    ->get()
                    ->map(function($suivi) {
                        return [
                            'id' => $suivi->id,
                            'date_heure' => $suivi->date_heure,
                            'action' => $suivi->action,
                            'action_libelle' => $suivi->action_libelle,
                            'action_color' => $suivi->action_color,
                            'message' => $suivi->message,
                            'badge_numero' => $suivi->badge->numero ?? null,
                            'utilisateur_nom' => $suivi->badge->utilisateur->nom_complet ?? 'Utilisateur supprimé'
                        ];
                    });

                return response()->json([
                    'success' => true,
                    'data' => [
                        'total_badges' => $totalBadges,
                        'badges_actifs' => $badgesActifs,
                        'badges_inactifs' => $badgesInactifs,
                        'badges_suspendus' => $badgesSuspendus,
                        'utilisations_aujourd_hui' => $utilisationsAujourdhui,
                        'dernieres_activites' => $dernieresActivites
                    ]
                ]);

            } catch (\Exception $e) {
                return response()->json([
                    'success' => false,
                    'message' => 'Erreur lors de la récupération des statistiques',
                    'error' => $e->getMessage()
                ], 500);
            }
        }

        /**
         * Récupérer l'historique d'activité d'un badge
         */
        public function activity($numero)
        {
            try {
                $badge = Badge::where('numero', $numero)->first();
                
                if (!$badge) {
                    return response()->json([
                        'success' => false,
                        'message' => 'Badge non trouvé'
                    ], 404);
                }

                // Récupérer l'historique du suivi_badge avec les informations utilisateur
                // Le numero du badge correspond à id_badge dans suivi_badge
                $activites = SuiviBadge::where('id_badge', $numero)
                    ->with(['badge.utilisateur'])
                    ->orderBy('date_heure', 'desc')
                    ->get()
                    ->map(function($suivi) use ($numero) {
                        return [
                            'id' => $suivi->id,
                            'action' => $this->mapActionToFrench($suivi->action),
                            'message' => $suivi->message ?: $this->mapActionToFrench($suivi->action),
                            'date_action' => $suivi->date_heure,
                            'date_heure' => $suivi->date_heure,
                            'created_at' => $suivi->date_heure,
                            'description' => $suivi->message,
                            'utilisateur' => $suivi->badge && $suivi->badge->utilisateur ? [
                                'prenom' => $suivi->badge->utilisateur->prenom,
                                'nom' => $suivi->badge->utilisateur->nom
                            ] : null,
                            'details' => [
                                'badge_numero' => $numero,
                                'action_original' => $suivi->action
                            ]
                        ];
                    });

                return response()->json([
                    'success' => true,
                    'data' => $activites,
                    'count' => $activites->count()
                ]);

            } catch (\Exception $e) {
                return response()->json([
                    'success' => false,
                    'message' => 'Erreur lors de la récupération de l\'activité',
                    'error' => $e->getMessage()
                ], 500);
            }
        }

        /**
         * Mapper les actions vers des clés de traduction
         */
        private function mapActionToFrench($action)
        {
            $mapping = [
                'création' => 'creation',
                'creation' => 'creation',
                'activation' => 'activation',
                'désactivation' => 'desactivation',
                'deactivation' => 'desactivation',
                'suspension' => 'suspension',
                'modification' => 'modification',
                'assignation' => 'assignation',
                'changement_statut' => 'changement_statut',
                'réactivation' => 'reactivation',
                'reactivation' => 'reactivation'
            ];

            return $mapping[$action] ?? $action;
        }
    }
?>


