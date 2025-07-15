<?php

namespace App\Http\Controllers;

use Illuminate\Http\Request;
use App\Models\Incident;

class IncidentController extends Controller
{
    public function index(Request $request)
    {
        $incidents = Incident::with('signaleur')->orderByDesc('datetime')->get();
        $data = $incidents->map(function ($incident) {
            return [
                'id' => $incident->id,
                'datetime' => $incident->datetime,
                'object' => $incident->object,
                'statut' => $incident->statut,
                'id_signaleur' => $incident->id_signaleur,
                'nom_signaleur' => $incident->signaleur ? $incident->signaleur->nom : null,
                'prenom_signaleur' => $incident->signaleur ? $incident->signaleur->prenom : null,
            ];
        });
        return response()->json(['success' => true, 'data' => $data]);
    }

    public function store(Request $request)
    {
        $validated = $request->validate([
            'datetime' => 'required|date',
            'object' => 'required|string',
            'statut' => 'required|in:a_venir,en_cours,resolu',
            'id_signaleur' => 'nullable|integer|exists:personne,id_personne',
        ]);
        $incident = Incident::create($validated);
        return response()->json(['success' => true, 'data' => $incident]);
    }

    public function destroy($id)
    {
        $incident = Incident::findOrFail($id);
        $incident->delete();
        return response()->json(['success' => true]);
    }
}
