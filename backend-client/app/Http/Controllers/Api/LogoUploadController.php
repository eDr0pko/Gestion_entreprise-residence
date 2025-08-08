<?php
    namespace App\Http\Controllers\Api;

    use Illuminate\Http\Request;
    use Illuminate\Support\Str;
    use Illuminate\Routing\Controller;

    class LogoUploadController extends Controller
    {
        // POST /api/upload-logo
        public function upload(Request $request)
        {
            if (!$request->hasFile('logo')) {
                return response()->json(['error' => 'Aucun fichier envoyé.'], 400);
            }
            $file = $request->file('logo');
            if (!$file->isValid()) {
                return response()->json(['error' => 'Fichier invalide.'], 400);
            }
            $filename = 'logo_' . Str::random(8) . '.' . $file->getClientOriginalExtension();
            $path = $file->storeAs('logos', $filename, 'public');
            // Retourne le chemin relatif pour stockage en BDD
            return response()->json(['url' => '/logos/' . $filename]);
        }
    }
?>


