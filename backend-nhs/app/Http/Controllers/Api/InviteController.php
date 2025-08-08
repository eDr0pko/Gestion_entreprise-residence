<?php

namespace App\Http\Controllers\Api;

use App\Http\Controllers\Controller;
use App\Models\Invite;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\Log;

class InviteController extends Controller
{
    public function index()
    {
        return Invite::all();
    }

    public function show($id)
    {
        $invite = Invite::find($id);
        if (!$invite) {
            return response()->json(['message' => 'Invitation non trouvée'], 404);
        }
        return response()->json($invite);
    }

    public function store(Request $request)
    {
        $invite = Invite::create($request->all());
        return response()->json($invite, 201);
    }

    public function update(Request $request, $id)
    {
        $invite = Invite::findOrFail($id);
        $invite->update($request->all());
        return response()->json($invite);
    }

    public function destroy($id)
    {
        Invite::destroy($id);
        return response()->json(null, 204);
    }

    // Récupérer les invitations de l'utilisateur connecté
    public function getInvites()
    {
        try {
            $user = Auth::user();

            if (!$user) {
                return response()->json([
                    'success' => false,
                    'message' => 'Utilisateur non authentifié'
                ], 401);
            }

            Log::info('Chargement des invitations pour l\'utilisateur: ' . $user->email);

            // Récupérer les invitations où l'utilisateur est destinataire
            $invites = Invite::where('email_destinataire', $user->email)
                ->with(['expediteur']) // Si tu as une relation 'expediteur' sur Invite
                ->orderBy('created_at', 'desc')
                ->get()
                ->map(function ($invite) {
                    return [
                        'id' => $invite->id_invitation,
                        'email_expediteur' => $invite->email_invite,
                        'email_destinataire' => $invite->email_destinataire,
                        'statut' => $invite->statut_invitation,
                        'date_envoi' => $invite->created_at ? $invite->created_at->toISOString() : null,
                        'expediteur_nom' => $invite->expediteur ? $invite->expediteur->nom_complet : null,
                        // Ajoute d'autres champs si besoin
                    ];
                });

            return response()->json([
                'success' => true,
                'invites' => $invites
            ]);
        } catch (\Exception $e) {
            Log::error('Erreur lors de la récupération des invitations: ' . $e->getMessage());
            return response()->json([
                'success' => false,
                'message' => 'Erreur lors de la récupération des invitations',
                'error' => $e->getMessage()
            ], 500);
        }
    }
}
