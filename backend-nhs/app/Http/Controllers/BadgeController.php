<?php

    namespace App\Http\Controllers;

    use App\Models\Badge;
    use App\Models\SuiviBadge;
    use App\Models\Personne;
    use Illuminate\Http\Request;
    use Illuminate\Support\Facades\DB;
    use Illuminate\Support\Facades\Validator;
    use Illuminate\Support\Facades\Auth;
    use Carbon\Carbon;

    class BadgeController extends Controller
    {
        /**
         * Liste tous les badges avec pagination et filtres
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
                    $query->whereHas('utilisateur', function($q) use ($search) {
                        $q->where('nom', 'LIKE', "%{$search}%")
                        ->orWhere('prenom', 'LIKE', "%{$search}%")
                        ->orWhere('email', 'LIKE', "%{$search}%");
                    })->orWhere('numero', 'LIKE', "%{$search}%")
                    ->orWhere('commentaire', 'LIKE', "%{$search}%")
                    ->orWhere('droit', 'LIKE', "%{$search}%");
                }

                if ($request->has('statut') && $request->statut) {
                    $statut = $request->statut;
                    if ($statut === 'actif') {
                        $query->whereHas('dernierSuivi', function($q) {
                            $q->where('action', '!=', 'desactivation')
                            ->where('action', '!=', 'désactivation');
                        })->orWhereDoesntHave('dernierSuivi');
                    } elseif ($statut === 'inactif') {
                        $query->whereHas('dernierSuivi', function($q) {
                            $q->where('action', 'desactivation')
                            ->orWhere('action', 'désactivation');
                        });
                    }
                }

                $badges = $query->orderBy('numero', 'desc')->get();

                // Enrichir les données pour le frontend
                $badgesData = $badges->map(function($badge) {
                    return [
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
         * Active un badge
         */
        public function activer($id)
        {
            return $this->changerStatut($id, 'activation', 'Badge activé');
        }

        /**
         * Désactive un badge
         */
        public function desactiver($id)
        {
            return $this->changerStatut($id, 'desactivation', 'Badge désactivé');
        }

        /**
         * Suspend un badge
         */
        public function suspendre($id)
        {
            return $this->changerStatut($id, 'suspension', 'Badge suspendu');
        }

        /**
         * Méthode privée pour changer le statut d'un badge
         */
        private function changerStatut($id, $action, $messageSucces)
        {
            try {
                $badge = Badge::findOrFail($id);

                DB::beginTransaction();

                SuiviBadge::create([
                    'date_heure' => Carbon::now(),
                    'id_badge' => $badge->numero,
                    'action' => $action,
                    'message' => $messageSucces . ' par ' . (Auth::user()->nom ?? 'Système')
                ]);

                DB::commit();

                $badge->load(['personne', 'dernierSuivi']);
                $badge->statut = $badge->statut;
                $badge->is_actif = $badge->is_actif;

                return response()->json([
                    'success' => true,
                    'data' => $badge,
                    'message' => $messageSucces . ' avec succès'
                ]);

            } catch (\Exception $e) {
                DB::rollBack();
                return response()->json([
                    'success' => false,
                    'message' => 'Erreur lors du changement de statut',
                    'error' => $e->getMessage()
                ], 500);
            }
        }

        /**
         * Obtient l'historique complet d'un badge
         */
        public function historique($id)
        {
            try {
                $badge = Badge::with('personne')->findOrFail($id);
                $historique = SuiviBadge::where('id_badge', $id)
                                    ->orderBy('date_heure', 'desc')
                                    ->get();

                return response()->json([
                    'success' => true,
                    'data' => [
                        'badge' => $badge,
                        'historique' => $historique
                    ],
                    'message' => 'Historique récupéré avec succès'
                ]);

            } catch (\Exception $e) {
                return response()->json([
                    'success' => false,
                    'message' => 'Erreur lors de la récupération de l\'historique',
                    'error' => $e->getMessage()
                ], 500);
            }
        }

        /**
         * Recherche de personnes pour attribution de badge
         */
        public function rechercherPersonnes(Request $request)
        {
            try {
                $search = $request->get('search', '');
                
                $personnes = Personne::where(function($query) use ($search) {
                    $query->where('nom', 'like', "%{$search}%")
                        ->orWhere('prenom', 'like', "%{$search}%")
                        ->orWhere('email', 'like', "%{$search}%");
                })
                ->limit(20)
                ->get(['id_personne', 'nom', 'prenom', 'email']);

                return response()->json([
                    'success' => true,
                    'data' => $personnes,
                    'message' => 'Recherche effectuée avec succès'
                ]);

            } catch (\Exception $e) {
                return response()->json([
                    'success' => false,
                    'message' => 'Erreur lors de la recherche',
                    'error' => $e->getMessage()
                ], 500);
            }
        }

        /**
         * Statistiques des badges (alias pour compatibilité)
         */
        public function stats()
        {
            try {
                $totalBadges = Badge::count();
                $badgesActifs = Badge::whereHas('dernierSuivi', function($q) {
                    $q->where('action', '!=', 'desactivation')
                    ->where('action', '!=', 'désactivation');
                })->orWhereDoesntHave('dernierSuivi')->count();
                
                $badgesInactifs = Badge::whereHas('dernierSuivi', function($q) {
                    $q->where('action', 'desactivation')
                    ->orWhere('action', 'désactivation');
                })->count();
                
                $utilisationsAujourdhui = SuiviBadge::where('action', 'utilisation')
                                                ->whereDate('date_heure', Carbon::today())
                                                ->count();

                $dernieresActivites = SuiviBadge::with(['badge.utilisateur'])
                    ->orderBy('date_heure', 'desc')
                    ->limit(5)
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
                            'utilisateur_nom' => $suivi->badge && $suivi->badge->utilisateur 
                                ? $suivi->badge->utilisateur->nom_complet 
                                : 'Utilisateur supprimé'
                        ];
                    });

                return response()->json([
                    'success' => true,
                    'data' => [
                        'total_badges' => $totalBadges,
                        'badges_actifs' => $badgesActifs,
                        'badges_inactifs' => $badgesInactifs,
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
         * Statistiques des badges (méthode originale)
         */
        public function statistiques()
        {
            try {
                $stats = [
                    'total_badges' => Badge::count(),
                    'badges_actifs' => Badge::whereHas('dernierSuivi', function($q) {
                        $q->where('action', '!=', 'desactivation');
                    })->orWhereDoesntHave('dernierSuivi')->count(),
                    'badges_inactifs' => Badge::whereHas('dernierSuivi', function($q) {
                        $q->where('action', 'desactivation');
                    })->count(),
                    'badges_suspendus' => Badge::whereHas('dernierSuivi', function($q) {
                        $q->where('action', 'suspension');
                    })->count(),
                    'derniere_utilisation' => SuiviBadge::where('action', 'utilisation')
                                                    ->latest('date_heure')
                                                    ->first(),
                    'utilisations_aujourdhui' => SuiviBadge::where('action', 'utilisation')
                                                        ->whereDate('date_heure', Carbon::today())
                                                        ->count()
                ];

                return response()->json([
                    'success' => true,
                    'data' => $stats,
                    'message' => 'Statistiques récupérées avec succès'
                ]);

            } catch (\Exception $e) {
                return response()->json([
                    'success' => false,
                    'message' => 'Erreur lors de la récupération des statistiques',
                    'error' => $e->getMessage()
                ], 500);
            }
        }
    }
?>


