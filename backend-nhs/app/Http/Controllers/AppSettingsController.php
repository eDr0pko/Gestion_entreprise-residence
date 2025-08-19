<?php

namespace App\Http\Controllers;

use Illuminate\Http\Request;
use App\Models\AppSetting;
use Illuminate\Support\Facades\Log;

class AppSettingsController extends Controller
{
    /**
     * Get public settings (accessible without authentication)
     */
    public function getPublicSettings()
    {
        try {
            $settings = AppSetting::all()->keyBy('setting_key');
            
            return response()->json([
                'success' => true,
                'settings' => [
                    'site_name' => $settings['site_name']->setting_value ?? 'Gestion Résidence',
                    'site_logo' => $settings['site_logo']->setting_value ?? null,
                    'primary_color' => $settings['primary_color']->setting_value ?? '#0097b2',
                    'secondary_color' => $settings['secondary_color']->setting_value ?? '#00b4d8',
                ]
            ]);
        } catch (\Exception $e) {
            Log::error('Erreur lors de la récupération des paramètres publics: ' . $e->getMessage());
            
            return response()->json([
                'success' => false,
                'message' => 'Erreur lors de la récupération des paramètres',
                'settings' => [
                    'site_name' => 'Gestion Résidence',
                    'site_logo' => null,
                    'primary_color' => '#0097b2',
                    'secondary_color' => '#00b4d8',
                ]
            ], 500);
        }
    }

    /**
     * Get all settings (admin only)
     */
    public function index()
    {
        try {
            $settings = AppSetting::all()->keyBy('setting_key');
            
            return response()->json([
                'success' => true,
                'settings' => $settings
            ]);
        } catch (\Exception $e) {
            Log::error('Erreur lors de la récupération des paramètres: ' . $e->getMessage());
            
            return response()->json([
                'success' => false,
                'message' => 'Erreur lors de la récupération des paramètres'
            ], 500);
        }
    }

    /**
     * Update settings (admin only)
     */
    public function update(Request $request)
    {
        try {
            $settingsData = $request->all();
            
            foreach ($settingsData as $key => $value) {
                AppSetting::updateOrCreate(
                    ['setting_key' => $key],
                    ['setting_value' => $value]
                );
            }
            
            return response()->json([
                'success' => true,
                'message' => 'Paramètres mis à jour avec succès'
            ]);
        } catch (\Exception $e) {
            Log::error('Erreur lors de la mise à jour des paramètres: ' . $e->getMessage());
            
            return response()->json([
                'success' => false,
                'message' => 'Erreur lors de la mise à jour des paramètres'
            ], 500);
        }
    }

    /**
     * Upload logo (admin only)
     */
    public function uploadLogo(Request $request)
    {
        try {
            $request->validate([
                'logo' => 'required|image|mimes:jpeg,png,jpg,gif|max:2048'
            ]);

            if ($request->hasFile('logo')) {
                $file = $request->file('logo');
                $filename = time() . '_logo.' . $file->getClientOriginalExtension();
                $file->move(public_path('uploads/logos'), $filename);
                
                AppSetting::updateOrCreate(
                    ['setting_key' => 'site_logo'],
                    ['setting_value' => 'uploads/logos/' . $filename]
                );
                
                return response()->json([
                    'success' => true,
                    'message' => 'Logo uploadé avec succès',
                    'logo_path' => 'uploads/logos/' . $filename
                ]);
            }
            
            return response()->json([
                'success' => false,
                'message' => 'Aucun fichier reçu'
            ], 400);
        } catch (\Exception $e) {
            Log::error('Erreur lors de l\'upload du logo: ' . $e->getMessage());
            
            return response()->json([
                'success' => false,
                'message' => 'Erreur lors de l\'upload du logo'
            ], 500);
        }
    }
}
