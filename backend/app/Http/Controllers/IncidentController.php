<?php

namespace App\Http\Controllers;

use Illuminate\Http\Request;
use App\Models\Incident;

class IncidentController extends Controller
{
    public function index(Request $request)
    {
        $incidents = Incident::orderByDesc('id')
            ->leftJoin('personne', 'incident.id_signaleur', '=', 'personne.id_personne')
            ->select(
                'incident.id',
                'incident.id_signaleur',
                'incident.datetime',
                'incident.object',
                'incident.statut',
                'incident.pieces_jointes',
                'personne.email as email_signaleur'
            )
            ->get();
        // Recompose details for frontend compatibility
        $incidents->transform(function($inc) {
            $details = [
                'datetime' => $inc->datetime,
                'object' => $inc->object,
                'statut' => $inc->statut,
                'email_signaleur' => $inc->email_signaleur,
                'pieces_jointes' => []
            ];
            if ($inc->pieces_jointes) {
                if (is_array($inc->pieces_jointes)) {
                    $details['pieces_jointes'] = $inc->pieces_jointes;
                } else {
                    // Try to decode JSON
                    try {
                        $pj = json_decode($inc->pieces_jointes, true);
                        if (is_array($pj)) $details['pieces_jointes'] = $pj;
                    } catch (\Exception $e) {}
                }
            }
            $inc->details = $details;
            return $inc;
        });
        return response()->json(['success' => true, 'data' => $incidents]);
    }

    public function store(Request $request)
    {
        try {
            if (!$request->has('details')) {
                return response()->json(['success' => false, 'message' => 'Le champ details est requis.'], 422);
            }
            $detailsRaw = $request->input('details');
            if (!is_string($detailsRaw)) {
                return response()->json(['success' => false, 'message' => 'Le champ details doit être une chaîne JSON.'], 422);
            }
            $detailsArr = json_decode($detailsRaw, true);
            if (!is_array($detailsArr)) {
                return response()->json(['success' => false, 'message' => 'Le champ details doit être un JSON valide.'], 422);
            }
            // Try to get id_signaleur from auth, fallback to details.email_signaleur
            $id_signaleur = null;
            if (auth()->check()) {
                $id_signaleur = auth()->id();
            } else if (!empty($detailsArr['email_signaleur'])) {
                $personne = \DB::table('personne')->where('email', $detailsArr['email_signaleur'])->first();
                if ($personne) {
                    $id_signaleur = $personne->id_personne;
                }
            }
            $pieces = [];
            if ($request->hasFile('pieces_jointes')) {
                foreach ($request->file('pieces_jointes') as $file) {
                    $path = $file->store('incidents', 'public');
                    $pieces[] = '/storage/' . $path;
                }
            }
            $detailsArr['pieces_jointes'] = $pieces;
            $incident = Incident::create([
                'datetime' => $detailsArr['datetime'] ?? now(),
                'object' => $detailsArr['object'] ?? '',
                'statut' => $detailsArr['statut'] ?? 'en_cours',
                'id_signaleur' => $id_signaleur,
                'pieces_jointes' => json_encode($detailsArr['pieces_jointes'] ?? []),
            ]);
            $detailsArr['id'] = $incident->id;
            return response()->json(['success' => true, 'data' => [
                'id' => $incident->id,
                'id_signaleur' => $incident->id_signaleur,
                'details' => $detailsArr,
                'email_signaleur' => $detailsArr['email_signaleur'] ?? null,
            ]]);
        } catch (\Exception $e) {
            return response()->json([
                'success' => false,
                'message' => 'Erreur serveur: ' . $e->getMessage(),
                'trace' => $e->getTraceAsString(),
            ], 500);
        }
    }

    public function destroy($id)
    {
        $incident = Incident::findOrFail($id);
        $incident->delete();
        return response()->json(['success' => true]);
    }
}
