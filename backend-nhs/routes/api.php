<?php

use Illuminate\Http\Request;
use Illuminate\Support\Facades\Route;
use App\Http\Controllers\ApiController;
use App\Http\Controllers\AuthController;
use App\Http\Controllers\MessageController;
use App\Http\Controllers\IncidentController;
use App\Http\Controllers\GuestController;
use App\Http\Controllers\AdminStatsController;
use App\Http\Controllers\LogController;
use App\Http\Controllers\LogAdminController;
use App\Http\Controllers\AppSettingsController;

/*
|--------------------------------------------------------------------------
| API Routes - Backend NHS (Business Logic)
|--------------------------------------------------------------------------
|
| Ce backend contient TOUTE la logique métier de l'application
| backend-client ne fait que du proxy vers ces endpoints
| Toutes les fonctionnalités de l'ancien backend sont ici
|
*/

//
// 🔓 Routes publiques
//
Route::get('/health', function () {
    return response()->json([
        'status' => 'ok',
        'server' => 'Laravel Backend-NHS (Business Logic)',
        'timestamp' => now(),
        'role' => 'business_logic_core'
    ]);
});

//
// 🔐 AUTHENTIFICATION
//
Route::post('/login', [AuthController::class, 'login']);
Route::post('/logout', [AuthController::class, 'logout']);

//
// 🎯 Routes publiques pour invités
//
Route::prefix('guests')->group(function () {
    Route::post('/register', [GuestController::class, 'register']);
    Route::post('/login', [GuestController::class, 'login']);
});

//
// 🎨 Routes publiques pour les paramètres d'app
//
Route::get('/settings/public', [AppSettingsController::class, 'getPublicSettings']);

// Route publique pour contacter l'admin (incidents non authentifiés)
Route::post('/contact-admin', [IncidentController::class, 'store']);

//
// 🔒 Routes protégées par authentification
//
Route::middleware('auth:sanctum')->group(function () {
    
    // 👤 Authentification et profil utilisateur
    Route::get('/user', [AuthController::class, 'me']);
    Route::get('/check', [AuthController::class, 'check']);
    Route::get('/profile', [AuthController::class, 'getProfile']);
    Route::get('/profile/stats', [AuthController::class, 'getProfileStats']);
    Route::put('/profile/update', [AuthController::class, 'updateProfile']);
    Route::post('/profile/verify-password', [AuthController::class, 'verifyPassword']);
    Route::put('/profile/password', [AuthController::class, 'updatePassword']);
    Route::post('/profile/avatar', [AuthController::class, 'uploadAvatar']);
    Route::delete('/profile/avatar', [AuthController::class, 'deleteAvatar']);

    // 💬 Messages et Conversations
    Route::get('/conversations', [MessageController::class, 'getConversations']);
    Route::post('/conversations', [MessageController::class, 'createConversation']);
    Route::get('/conversations/users', [MessageController::class, 'getAvailableUsers']);
    Route::get('/conversations/{groupId}/members', [MessageController::class, 'getGroupMembers']);
    Route::post('/conversations/{groupId}/members', [MessageController::class, 'addGroupMembers']);
    Route::get('/messages/{groupId}', [MessageController::class, 'getMessages']);
    Route::post('/messages/{groupId}', [MessageController::class, 'sendMessage']);
    Route::post('/conversations/{groupId}/mark-read', [MessageController::class, 'markAsRead']);

    // 💬 Rafraîchissement auto
    Route::get('/conversations/check-changes', [MessageController::class, 'checkConversationsChanges']);
    Route::get('/messages/{groupId}/check-changes', [MessageController::class, 'checkMessagesChanges']);
    Route::post('/messages/{messageId}/reactions', [MessageController::class, 'addReaction']);

    // 📁 Fichiers
    Route::get('/files/{fichierId}', [MessageController::class, 'downloadFile']);
    Route::get('/avatars/{filename}', [AuthController::class, 'getAvatar']);

    // 🏥 Incidents
    Route::get('/incidents', [IncidentController::class, 'index']);
    Route::post('/incidents', [IncidentController::class, 'store']);
    Route::get('/incidents/{id}', [IncidentController::class, 'show']);
    Route::put('/incidents/{id}', [IncidentController::class, 'update']);
    Route::delete('/incidents/{id}', [IncidentController::class, 'destroy']);

    // 👥 Gestion des invités
    Route::get('/guests', [GuestController::class, 'index']);
    Route::post('/guests/{id}/deactivate', [GuestController::class, 'deactivate']);

    // 📅 Visites (création avec logique métier)
    Route::post('/visites', [App\Http\Controllers\Api\VisiteController::class, 'store']);
    Route::get('/visites', [App\Http\Controllers\Api\VisiteController::class, 'getUserVisits']);
    Route::post('/visites/{id}/statut', [App\Http\Controllers\Api\VisiteController::class, 'updateStatus']);

    // 🎛️ Administration
    Route::prefix('admin')->group(function () {
        
        // Messages (modération)
        Route::get('/messages', [MessageController::class, 'getAllMessages']);
        Route::delete('/conversations/{groupId}', [MessageController::class, 'deleteConversation']);
        
        // Incidents
        Route::get('/incidents', [IncidentController::class, 'index']);
        Route::post('/incidents', [IncidentController::class, 'store']);
        Route::delete('/incidents/{id}', [IncidentController::class, 'destroy']);
        
        // Logs d'activité
        Route::get('/logs', [LogController::class, 'index']);
        Route::post('/logs/delete-before', [LogAdminController::class, 'deleteBefore']);
        
        // Statistiques
        Route::get('/stats', [AdminStatsController::class, 'index']);
        
        // Gestion utilisateurs
        Route::get('/persons', [AuthController::class, 'getAllPersons']);
        Route::put('/persons/{id}', [AuthController::class, 'updatePerson']);
        Route::post('/persons', [AuthController::class, 'createPerson']);
        Route::delete('/persons/{id}', [AuthController::class, 'deletePerson']);
        Route::post('/users/{id}/ban', [AuthController::class, 'banUser']);
        Route::post('/users/{id}/unban', [AuthController::class, 'unbanUser']);
        
        // Invités (administration)
        Route::get('/guests', [GuestController::class, 'index']);
        Route::post('/guests', [GuestController::class, 'store']);
        Route::put('/guests/{id}', [GuestController::class, 'update']);
        Route::delete('/guests/{id}', [GuestController::class, 'destroy']);
        Route::post('/guests/{id}/deactivate', [GuestController::class, 'deactivate']);
        
        // 🎨 Paramètres d'application
        Route::get('/settings', [AppSettingsController::class, 'index']);
        Route::put('/settings', [AppSettingsController::class, 'update']);
        Route::post('/settings/logo', [AppSettingsController::class, 'uploadLogo']);
    });
});

//
// 🚀 NHS - Fonctionnalités propriétaires avancées
//
Route::prefix('nhs')->group(function () {
    
    /**
     * Status du service NHS
     */
    Route::get('/status', [ApiController::class, 'status']);
    
    /**
     * Analyses avancées et prédictives
     */
    Route::get('/advanced-analytics', [ApiController::class, 'advancedAnalytics']);
    
    /**
     * Automatisations intelligentes
     */
    Route::get('/smart-automation', [ApiController::class, 'smartAutomation']);
    
    /**
     * Maintenance prédictive
     */
    Route::get('/predictive-maintenance', [ApiController::class, 'predictiveMaintenance']);
    
    /**
     * Surveillance sécurité avancée
     */
    Route::get('/security-monitoring', [ApiController::class, 'securityMonitoring']);
    
});

//
// 🧪 Routes de test et debug
//
Route::get('/test-conversation', function () {
    try {
        $user = \App\Models\Personne::where('email', 'marie.durand@residence.com')->first();
        if (!$user) {
            return response()->json(['error' => 'User not found']);
        }

        $conversations = \App\Models\GroupeMessage::whereHas('personnes', function ($query) use ($user) {
            $query->where('personne_groupe.id_personne', $user->id_personne);
        })->get();

        return response()->json([
            'user_id' => $user->id_personne,
            'conversations_count' => $conversations->count(),
            'success' => true,
            'backend' => 'NHS Business Logic'
        ]);
    } catch (\Exception $e) {
        return response()->json([
            'error' => $e->getMessage(),
            'line' => $e->getLine(),
            'file' => $e->getFile()
        ], 500);
    }
});
