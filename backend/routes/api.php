<?php

use Illuminate\Http\Request;
use Illuminate\Support\Facades\Route;
use App\Http\Controllers\AuthController;
use App\Http\Controllers\MessageController;
use App\Http\Controllers\GuestController;

/*
|--------------------------------------------------------------------------
| API Routes
|--------------------------------------------------------------------------
|
| Here is where you can register API routes for your application. These
| routes are loaded by the RouteServiceProvider and all of them will
| be assigned to the "api" middleware group. Make something great!
|
*/

// Routes publiques
Route::post('/login', [AuthController::class, 'login']);
Route::get('/health', function () {
    return response()->json([
        'status' => 'ok',
        'server' => 'Laravel',
        'timestamp' => now()
    ]);
});
Route::get('/db-check', function () {
    try {
        $userCount = \App\Models\Personne::count();
        $groupCount = \App\Models\GroupeMessage::count();
        
        if ($userCount > 0) {
            $user = \App\Models\Personne::first();
            // Test de la requête corrigée
            $conversations = \App\Models\GroupeMessage::whereHas('personnes', function ($query) use ($user) {
                $query->where('personne_groupe.id_personne', $user->id_personne);
            })->count();
            
            return response()->json([
                'status' => 'database_ok',
                'users_count' => $userCount,
                'groups_count' => $groupCount,
                'sample_user_id' => $user->id_personne,
                'sample_user_email' => $user->email,
                'user_conversations' => $conversations,
                'sql_fix' => 'working'
            ]);
        } else {
            return response()->json([
                'status' => 'database_empty',
                'message' => 'Aucun utilisateur trouvé. Exécutez le script creationdb_corrected.sql'
            ]);
        }
    } catch (\Exception $e) {
        return response()->json([
            'status' => 'database_error',
            'error' => $e->getMessage(),
            'message' => 'Erreur de base de données - vérifiez la configuration'
        ], 500);
    }
});

Route::get('/visite', [VisiteController::class, 'getUserVisits']);

// Routes pour les invités (publiques)
Route::prefix('guests')->group(function () {
    Route::post('/register', [GuestController::class, 'register']);
    Route::post('/login', [GuestController::class, 'login']);
});

// Routes protégées par authentification (membres avec token)
Route::middleware('auth:sanctum')->group(function () {
    // Auth
    Route::post('/logout', [AuthController::class, 'logout']);
    Route::get('/user', [AuthController::class, 'me']);
    Route::get('/check', [AuthController::class, 'check']);
});

// Routes accessibles aux membres et invités (token-based auth)
Route::middleware('auth:sanctum')->group(function () {
    // Messages
    Route::get('/conversations', [MessageController::class, 'getConversations']);
    Route::post('/conversations', [MessageController::class, 'createConversation']);
    Route::get('/conversations/users', [MessageController::class, 'getAvailableUsers']);
    Route::get('/conversations/{groupId}/members', [MessageController::class, 'getGroupMembers']);
    Route::post('/conversations/{groupId}/members', [MessageController::class, 'addGroupMembers']);
    Route::get('/messages/{groupId}', [MessageController::class, 'getMessages']);
    Route::post('/messages/{groupId}', [MessageController::class, 'sendMessage']);
    Route::post('/conversations/{groupId}/mark-read', [MessageController::class, 'markAsRead']);

    //Visites
    //Route::get('/visite', [VisiteController::class, 'getUserVisits']);



    
    // Endpoints optimisés pour le rafraîchissement automatique
    Route::get('/conversations/check-changes', [MessageController::class, 'checkConversationsChanges']);
    Route::get('/messages/{groupId}/check-changes', [MessageController::class, 'checkMessagesChanges']);
    
    // Nouvelles routes
    Route::post('/messages/{messageId}/reactions', [MessageController::class, 'addReaction']);
    
    // Administration des invités
    Route::prefix('admin/guests')->group(function () {
        Route::get('/', [GuestController::class, 'index']);
        Route::post('/{id}/deactivate', [GuestController::class, 'deactivate']);
    });
    Route::get('/files/{fichierId}', [MessageController::class, 'downloadFile']);
    Route::get('/avatars/{filename}', [AuthController::class, 'getAvatar']);
    
    // Profil utilisateur
    Route::get('/profile/stats', [AuthController::class, 'getProfileStats']);
    Route::put('/profile/update', [AuthController::class, 'updateProfile']);
    Route::post('/profile/verify-password', [AuthController::class, 'verifyPassword']);
    Route::put('/profile/password', [AuthController::class, 'updatePassword']);
    Route::post('/profile/avatar', [AuthController::class, 'uploadAvatar']);
    Route::delete('/profile/avatar', [AuthController::class, 'deleteAvatar']);
    
    // Gestion des invités temporaires (pour les administrateurs)
    Route::get('/guests', [GuestController::class, 'index']);
    Route::post('/guests/{id}/deactivate', [GuestController::class, 'deactivate']);
});

// Routes de test temporaire
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
            'success' => true
        ]);
    } catch (\Exception $e) {
        return response()->json([
            'error' => $e->getMessage(),
            'line' => $e->getLine(),
            'file' => $e->getFile()
        ], 500);
    }
});


