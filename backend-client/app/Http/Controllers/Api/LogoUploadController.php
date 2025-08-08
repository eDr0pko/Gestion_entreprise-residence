<?php
    namespace App\Http\Controllers\Api;

    use Illuminate\Http\Request;
    use Illuminate\Support\Str;
    use Illuminate\Routing\Controller;

    class LogoUploadController extends Controller
    {
        // POST /api/settings/upload-logo
        public function upload(Request $request)
        {
            try {
                if (!$request->hasFile('logo')) {
                    return response()->json(['error' => 'Aucun fichier envoyé.'], 400);
                }
                
                $file = $request->file('logo');
                if (!$file->isValid()) {
                    return response()->json(['error' => 'Fichier invalide.'], 400);
                }
                
                // Validation du type de fichier
                if (!in_array($file->getMimeType(), ['image/jpeg', 'image/jpg', 'image/png', 'image/gif', 'image/webp'])) {
                    return response()->json(['error' => 'Format de fichier non supporté. Utilisez JPG, PNG, GIF ou WebP.'], 400);
                }
                
                // Validation de la taille (2MB max)
                if ($file->getSize() > 2 * 1024 * 1024) {
                    return response()->json(['error' => 'Le fichier doit faire moins de 2MB.'], 400);
                }
                
                // Créer le dossier s'il n'existe pas
                $logoDir = public_path('logos');
                if (!file_exists($logoDir)) {
                    mkdir($logoDir, 0755, true);
                }
                
                // Générer un nom de fichier unique
                $filename = 'logo_' . time() . '_' . Str::random(8) . '.' . $file->getClientOriginalExtension();
                
                // Déplacer le fichier
                $file->move($logoDir, $filename);
                
                // Retourne le chemin relatif pour stockage en BDD
                return response()->json(['logoUrl' => '/logos/' . $filename]);
            } catch (\Exception $e) {
                return response()->json(['error' => 'Erreur lors du téléchargement: ' . $e->getMessage()], 500);
            }
        }
    }
?>


