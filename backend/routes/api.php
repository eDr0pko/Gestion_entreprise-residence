<?php

use Illuminate\Http\Request;
use Illuminate\Support\Facades\Route;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\DB;
use App\Http\Controllers\AuthController;
use App\Http\Controllers\MessageController;
use App\Http\Controllers\GuestController;
use App\Http\Controllers\VisiteController;

/*
|--------------------------------------------------------------------------
| API Routes
|--------------------------------------------------------------------------
*/

//
// 🔓 Routes publiques
//
Route::post('/login', [AuthController::class, 'login']);
Route::get('/health', function () {
    return response()->json([
        'status' => 'ok',
        'server' => 'Laravel',
        'timestamp' => now()
    ]);
});

// Vérification de la base de données
Route::get('/db-check', function () {
    try {
        $userCount = \App\Models\Personne::count();
        $groupCount = \App\Models\GroupeMessage::count();
        
        if ($userCount > 0) {
            $user = \App\Models\Personne::first();

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

//
// 🔐 Routes protégées par auth:sanctum
//
Route::middleware('auth:sanctum')->group(function () {
    // Incidents (admin)
    Route::get('/admin/incidents', [\App\Http\Controllers\IncidentController::class, 'index']);
    Route::post('/admin/incidents', [\App\Http\Controllers\IncidentController::class, 'store']);
    Route::delete('/admin/incidents/{id}', [\App\Http\Controllers\IncidentController::class, 'destroy']);
    // Logs d'activité (admin)
    Route::get('/admin/logs', [\App\Http\Controllers\LogController::class, 'index']);
    Route::post('/admin/logs/delete-before', [\App\Http\Controllers\LogAdminController::class, 'deleteBefore']);
    // Statistiques admin
    Route::get('/admin/stats', [\App\Http\Controllers\AdminStatsController::class, 'index']);
    // Auth
    Route::post('/logout', [AuthController::class, 'logout']);
    Route::get('/user', [AuthController::class, 'me']);
    Route::get('/check', [AuthController::class, 'check']);

    // Gestion utilisateurs (admin)
    Route::get('/admin/persons', [AuthController::class, 'getAllPersons']);
    Route::put('/admin/persons/{id}', [AuthController::class, 'updatePerson']);
    Route::post('/admin/persons', [AuthController::class, 'createPerson']);
    Route::delete('/admin/persons/{id}', [AuthController::class, 'deletePerson']);

    // 👤 Profil
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

    // Modération : tous les messages (admin)
    Route::get('/admin/messages', [MessageController::class, 'getAllMessages']);
    Route::delete('/admin/conversations/{groupId}', [MessageController::class, 'deleteConversation']);

    // Modération : tous les messages (admin)
    Route::get('/admin/messages', [MessageController::class, 'getAllMessages']);
    Route::delete('/admin/conversations/{groupId}', [MessageController::class, 'deleteConversation']);

    // 💬 Rafraîchissement auto
    Route::get('/conversations/check-changes', [MessageController::class, 'checkConversationsChanges']);
    Route::get('/messages/{groupId}/check-changes', [MessageController::class, 'checkMessagesChanges']);
    Route::post('/messages/{messageId}/reactions', [MessageController::class, 'addReaction']);

    // 📁 Fichiers
    Route::get('/files/{fichierId}', [MessageController::class, 'downloadFile']);
    Route::get('/avatars/{filename}', [AuthController::class, 'getAvatar']);

    // 👥 Administration des invités
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

    // 📅 VISITES : Récupération des visites liées à l'utilisateur connecté
    Route::get('/visites', function () {
        $user = Auth::user();

        $visites = DB::table('visite')
            ->where('email_visiteur', $user->email)
            ->orderBy('date_visite_start', 'desc')
            ->get();

        return response()->json([
            'success' => true,
            'visites' => $visites
        ]);
    });

    // 📅 VISITES : Modification du statut d'une visite
    Route::post('/visites/{id}/statut', function (Request $request, $id) {
        $user = Auth::user();
        $statut = $request->input('statut');

        $statutsAutorises = ['en_cours', 'annulee', 'terminee', 'programmee', 'banni'];

        if (!in_array($statut, $statutsAutorises)) {
            return response()->json(['success' => false, 'message' => 'Statut invalide'], 400);
        }

        $visite = DB::table('visite')
            ->where('id_visite', $id)
            ->where('email_visiteur', $user->email)
            ->first();

        if (!$visite) {
            return response()->json(['success' => false, 'message' => 'Visite non trouvée'], 404);
        }

        DB::table('visite')
            ->where('id_visite', $id)
            ->update(['statut_visite' => $statut]);

        return response()->json(['success' => true, 'message' => 'Statut mis à jour']);
    });

    // 📅 VISITES : Création d'une nouvelle visite
    Route::post('/visites', [VisiteController::class, 'store']);
});

//
// 🎯 Routes publiques pour invités
//
Route::prefix('guests')->group(function () {
    Route::post('/register', [GuestController::class, 'register']);
    Route::post('/login', [GuestController::class, 'login']);
});

//
// 🧪 Test API temporaire
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
