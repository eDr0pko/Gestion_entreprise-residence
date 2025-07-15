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
                'pieces_jointes' => $incident->pieces_jointes,
            ];
        });
        return response()->json(['success' => true, 'data' => $data]);
    }

    public function store(Request $request)
    {
        \Log::info('Incident store request', ['body' => $request->all()]);
        try {
            $validated = $request->validate([
                'datetime' => 'required|date',
                'object' => 'required|string',
                'statut' => 'required|in:a_venir,en_cours,resolu',
                'id_signaleur' => 'nullable|integer|exists:personne,id_personne',
                'pieces_jointes' => 'nullable',
            ]);
        } catch (\Illuminate\Validation\ValidationException $e) {
            \Log::error('Incident validation failed', ['errors' => $e->errors(), 'body' => $request->all()]);
            return response()->json(['success' => false, 'message' => 'Validation error', 'errors' => $e->errors()], 422);
        }
        // Si pieces_jointes est une string JSON, la convertir en array
        if (isset($validated['pieces_jointes']) && is_string($validated['pieces_jointes'])) {
            $decoded = json_decode($validated['pieces_jointes'], true);
            if (json_last_error() === JSON_ERROR_NONE) {
                $validated['pieces_jointes'] = $decoded;
            } else {
                \Log::error('Invalid JSON for pieces_jointes', ['value' => $validated['pieces_jointes']]);
                $validated['pieces_jointes'] = null;
            }
        }
        \Log::info('Incident validated data', ['validated' => $validated]);
        $incident = Incident::create($validated);
        return response()->json(['success' => true, 'data' => $incident]);
    }

    // Endpoint pour upload de fichiers liés à un incident
    public function upload(Request $request)
    {
        $request->validate([
            'file' => 'required|file|max:10240', // 10 Mo max
        ]);
        $file = $request->file('file');
        $path = $file->store('incidents', 'public');
        $url = asset('storage/' . $path);
        return response()->json(['success' => true, 'url' => $url]);
    }

    public function destroy($id)
    {
        $incident = Incident::findOrFail($id);
        $incident->delete();
        return response()->json(['success' => true]);
    }
}
