<?php

namespace App\Http\Controllers\Api;

use App\Http\Controllers\Controller;
use App\Models\Invite;
use App\Models\Visite;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\Log;


class VisiteController extends Controller
{
    /**
     * Récupère les visites liées à l'utilisateur connecté
     */
    public function getUserVisits()
    {
        try {
            $user = Auth::user();

            if (!$user) {
                return response()->json([
                    'success' => false,
                    'message' => 'Utilisateur non authentifié'
                ], 401);
            }

            Log::info('Chargement des visites pour l\'utilisateur: ' . $user->email);

            // Récupérer toutes les visites selon le rôle de l'utilisateur
            $query = Visite::query();
            
            // Si c'est un résident, récupérer ses visites
            if ($user->resident) {
                $query->where('id_invite', $user->id_personne);
            }
            // Si c'est un administrateur ou gardien, récupérer toutes les visites
            else if ($user->admin || $user->gardien) {
                // Pas de filtre, toutes les visites
            }
            // Si c'est un invité, récupérer ses visites en tant que visiteur
            else {
                $query->where('email_visiteur', $user->email);
            }

            $visites = $query->orderBy('date_visite_start', 'desc')->get();

            Log::info('Visites trouvées: ' . count($visites));

            return response()->json([
                'success' => true,
                'visites' => $visites
            ]);

        } catch (\Exception $e) {
            Log::error('Erreur lors de la récupération des visites: ' . $e->getMessage());
            Log::error('Stack trace: ' . $e->getTraceAsString());

            return response()->json([
                'success' => false,
                'message' => 'Erreur lors de la récupération des visites',
                'error' => $e->getMessage()
            ], 500);
        }
    }

    /**
     * Stocke une nouvelle visite
     */
    public function store(Request $request)
    {
        try {
            $user = Auth::user();

            if (!$user) {
                return response()->json([
                    'success' => false,
                    'message' => 'Utilisateur non authentifié'
                ], 401);
            }

            $validated = $request->validate([
                'motif_visite' => 'required|string',
                'email_visiteur' => 'nullable|email',
                'date_visite_start' => 'required|date',
                'date_visite_end' => 'required|date',
                'statut_visite' => 'required|string',
                'id_invite' => 'required|integer|exists:personne,id_personne',
                // Champs optionnels pour les nouveaux invités
                'nom_invite' => 'nullable|string',
                'prenom_invite' => 'nullable|string',
                'tel_invite' => 'nullable|string',
            ]);

            $visite = new Visite();
            $visite->motif_visite = $validated['motif_visite'];
            $visite->email_visiteur = $validated['email_visiteur'] ?? '';
            $visite->date_visite_start = $validated['date_visite_start'];
            $visite->date_visite_end = $validated['date_visite_end'];
            $visite->statut_visite = $validated['statut_visite'];
            $visite->id_invite = $validated['id_invite'];
            $visite->save();

            Log::info('Visite créée avec succès: ' . $visite->id_visite);

            return response()->json([
                'success' => true,
                'message' => 'Visite enregistrée avec succès',
                'visite' => $visite,
                'id' => $visite->id_visite
            ], 201);

        } catch (\Illuminate\Validation\ValidationException $e) {
            Log::error('Erreur de validation visite: ' . json_encode($e->errors()));
            return response()->json([
                'success' => false,
                'message' => 'Erreur de validation',
                'errors' => $e->errors()
            ], 422);
        } catch (\Exception $e) {
            Log::error('Erreur lors de l\'enregistrement de la visite: ' . $e->getMessage());
            return response()->json([
                'success' => false,
                'message' => 'Erreur lors de l\'enregistrement de la visite',
                'error' => $e->getMessage()
            ], 500);
        }
    }

    /**
     * Met à jour le statut d'une visite
     */
    public function updateStatus(Request $request, $id)
    {
        try {
            $user = Auth::user();

            if (!$user) {
                return response()->json([
                    'success' => false,
                    'message' => 'Utilisateur non authentifié'
                ], 401);
            }

            $validated = $request->validate([
                'statut' => 'required|string|in:programmee,en_cours,terminee,annulee'
            ]);

            $visite = Visite::find($id);

            if (!$visite) {
                return response()->json([
                    'success' => false,
                    'message' => 'Visite non trouvée'
                ], 404);
            }

            $visite->statut_visite = $validated['statut'];
            $visite->save();

            Log::info("Statut de la visite {$id} changé vers: " . $validated['statut']);

            return response()->json([
                'success' => true,
                'message' => 'Statut mis à jour avec succès',
                'visite' => $visite
            ]);

        } catch (\Illuminate\Validation\ValidationException $e) {
            return response()->json([
                'success' => false,
                'message' => 'Erreur de validation',
                'errors' => $e->errors()
            ], 422);
        } catch (\Exception $e) {
            Log::error('Erreur lors de la mise à jour du statut: ' . $e->getMessage());
            return response()->json([
                'success' => false,
                'message' => 'Erreur lors de la mise à jour du statut',
                'error' => $e->getMessage()
            ], 500);
        }
    }
}
