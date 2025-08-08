<?php

namespace App\Http\Controllers;

use Illuminate\Http\Request;

/**
 * MessageController simplifié pour backend-client
 * Ne contient AUCUNE logique métier - seulement accès aux fichiers locaux
 * Toute la logique métier est déplacée vers backend-nhs
 */
class MessageController extends Controller
{
    /**
     * Téléchargement de fichier (accès local uniquement)
     */
    public function downloadFile($fichierId)
    {
        // Cette méthode ne fait que servir les fichiers stockés localement
        // La logique de sécurité et validation est dans backend-nhs
        $filePath = storage_path('app/public/uploads/messages/' . $fichierId);
        
        if (!file_exists($filePath)) {
            return response()->json(['error' => 'Fichier non trouvé'], 404);
        }

        return response()->file($filePath);
    }
}
