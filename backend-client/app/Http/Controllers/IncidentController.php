<?php

namespace App\Http\Controllers;

use Illuminate\Http\Request;
use Illuminate\Support\Facades\Storage;
use Illuminate\Support\Facades\Http;
use Illuminate\Support\Facades\Log;

class IncidentController extends Controller
{
    /**
     * Stocke un nouvel incident avec upload local, puis proxy vers NHS
     */
    public function store(Request $request)
    {
        // 1. Gérer l'upload local
        $pieces = [];
        if ($request->hasFile('pieces_jointes')) {
            foreach ($request->file('pieces_jointes') as $file) {
                $path = $file->store('incidents', 'public');
                $pieces[] = '/storage/' . $path;
            }
        }

        // 2. Préparer les détails
        $detailsRaw = $request->input('details');
        $detailsArr = is_string($detailsRaw) ? json_decode($detailsRaw, true) : [];
        if (!is_array($detailsArr)) $detailsArr = [];
        $detailsArr['pieces_jointes'] = $pieces;

        // 3. Proxy la requête vers NHS (sans les fichiers)
        $nhsUrl = rtrim(env('NHS_API_URL', 'http://localhost:8001'), '/') . '/api/admin/incidents';
        $token = $request->bearerToken();
        $headers = [
            'Accept' => 'application/json',
        ];
        if ($token) $headers['Authorization'] = 'Bearer ' . $token;

        // Log pour debug
        Log::info('POST incident to NHS', [
            'url' => $nhsUrl,
            'details' => $detailsArr
        ]);

        $response = Http::withHeaders($headers)
            ->timeout(30)
            ->post($nhsUrl, [
                'details' => json_encode($detailsArr)
            ]);

        if ($response->successful()) {
            return response()->json($response->json(), $response->status());
        } else {
            return response()->json([
                'success' => false,
                'message' => "Erreur lors de la création de l'incident côté NHS",
                'nhs_response' => $response->body(),
            ], $response->status());
        }
    }
}
