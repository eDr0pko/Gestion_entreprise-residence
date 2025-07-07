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

            $visites = Visite::where('email_utilisateur', $user->email)
                ->with('invites') // si tu as une relation "invites()" dans le modèle Visite
                ->get()
                ->map(function ($visite) {
                    return [
                        'id' => $visite->id,
                        'date_visite' => $visite->date_visite ? $visite->date_visite->toISOString() : null,
                        'statut_visite' => $visite->statut_visite,
                        'invites' => $visite->invites->map(function ($invite) {
                            return [
                                'id' => $invite->id,
                                'nom' => $invite->nom,
                                'email' => $invite->email,
                            ];
                        }),
                    ];
                });

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
}
