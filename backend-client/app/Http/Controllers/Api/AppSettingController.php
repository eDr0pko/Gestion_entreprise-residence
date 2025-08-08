<?php
    namespace App\Http\Controllers\Api;

    use App\Models\AppSetting;
    use Illuminate\Http\Request;
    use Illuminate\Http\Response;
    use Illuminate\Routing\Controller;
    use Illuminate\Support\Facades\DB;

    class AppSettingController extends Controller
    {
        // GET /api/settings
        public function index()
        {
            try {
                $settings = DB::table('app_settings')->first();
                
                if (!$settings) {
                    // Créer des paramètres par défaut si ils n'existent pas
                    $defaultSettings = [
                        'app_name' => 'Gestion Résidence',
                        'company_name' => 'Ma Société',
                        'logo_url' => null,
                        'app_tagline' => 'Gérez votre résidence facilement',
                        'primary_color' => '#3B82F6',
                        'secondary_color' => '#10B981',
                        'accent_color' => '#F59E0B',
                        'background_color' => '#F8FAFC',
                        'welcome_title' => 'Bienvenue',
                        'welcome_message' => 'Bienvenue dans votre espace de gestion',
                        'updated_at' => now()
                    ];
                    
                    DB::table('app_settings')->insert($defaultSettings);
                    $settings = (object) $defaultSettings;
                }
                
                return response()->json($settings);
            } catch (\Exception $e) {
                return response()->json(['error' => 'Erreur lors du chargement des paramètres'], 500);
            }
        }

        // POST /api/settings
        public function store(Request $request)
        {
            try {
                // Valider seulement les champs qui existent dans la table app_settings
                $validated = $request->validate([
                    'app_name' => 'required|string|max:255',
                    'company_name' => 'nullable|string|max:255',
                    'logo_url' => 'nullable|string|max:512',
                    'app_tagline' => 'nullable|string|max:512',
                    'primary_color' => 'nullable|string|max:7',
                    'secondary_color' => 'nullable|string|max:7',
                    'accent_color' => 'nullable|string|max:7',
                    'background_color' => 'nullable|string|max:7',
                    'welcome_title' => 'nullable|string|max:255',
                    'welcome_message' => 'nullable|string|max:512'
                ]);

                $validated['updated_at'] = now();

                // Vérifier si des paramètres existent déjà
                $existingSettings = DB::table('app_settings')->first();
                
                if ($existingSettings) {
                    DB::table('app_settings')->update($validated);
                } else {
                    DB::table('app_settings')->insert($validated);
                }

                return response()->json(['message' => 'Paramètres mis à jour avec succès']);
            } catch (\Illuminate\Validation\ValidationException $e) {
                return response()->json(['error' => 'Données invalides', 'details' => $e->errors()], 422);
            } catch (\Exception $e) {
                return response()->json(['error' => 'Erreur lors de la sauvegarde'], 500);
            }
        }

        // Legacy methods for compatibility
        public function show()
        {
            return $this->index();
        }

        public function update(Request $request)
        {
            return $this->store($request);
        }
    }
?>


