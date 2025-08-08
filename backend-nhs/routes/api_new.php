<?php

    use Illuminate\Http\Request;
    use Illuminate\Support\Facades\Route;
    use App\Http\Controllers\ApiController;

    /*
    |--------------------------------------------------------------------------
    | API Routes - Backend NHS
    |--------------------------------------------------------------------------
    |
    | Routes spécifiques aux fonctionnalités NHS propriétaires
    | Ce backend fonctionne sur le port 8001 et contient les endpoints
    | considérés comme propriétaires et non distribuables.
    |
    */

    /**
     * Route de base pour vérifier que l'API NHS fonctionne
     * GET /api/user - Retourne les informations de l'utilisateur authentifié
     */
    Route::middleware('auth:sanctum')->get('/user', function (Request $request) {
        return $request->user();
    });

    /**
     * Groupe de routes NHS - Fonctionnalités propriétaires
     * Préfixe: /api/nhs/
     */
    Route::prefix('nhs')->group(function () {
        
        /**
         * Status du service NHS
         * GET /api/nhs/status
         */
        Route::get('/status', [ApiController::class, 'status']);
        
        /**
         * Analyses avancées et prédictives
         * GET /api/nhs/advanced-analytics
         * 
         * Retourne des analyses avancées sur:
         * - Prédiction d'occupation
         * - Optimisation énergétique
         * - Satisfaction des résidents
         */
        Route::get('/advanced-analytics', [ApiController::class, 'advancedAnalytics']);
        
        /**
         * Automatisations intelligentes
         * GET /api/nhs/smart-automation
         * 
         * Gère les automatisations intelligentes:
         * - Règles actives
         * - Suggestions d'automatisation
         * - Économies réalisées
         */
        Route::get('/smart-automation', [ApiController::class, 'smartAutomation']);
        
        /**
         * Maintenance prédictive
         * GET /api/nhs/predictive-maintenance
         * 
         * Fournit des informations sur:
         * - Alertes urgentes
         * - Maintenance programmée  
         * - État de santé des équipements
         */
        Route::get('/predictive-maintenance', [ApiController::class, 'predictiveMaintenance']);
        
        /**
         * Surveillance sécurité avancée
         * GET /api/nhs/security-monitoring
         * 
         * Monitoring sécuritaire avancé:
         * - Niveau de menace
         * - Anomalies d'accès
         * - État des systèmes
         */
        Route::get('/security-monitoring', [ApiController::class, 'securityMonitoring']);
        
    });
?>


