<?php

    use Illuminate\Http\Request;
    use Illuminate\Support\Facades\Route;
    use App\Http\Controllers\UnifiedProxyController;
    use App\Http\Controllers\DatabaseController;
    use App\Http\Controllers\AuthController;
    use App\Http\Controllers\MessageController;
    use App\Http\Controllers\Api\LogoUploadController;

    /*
    |--------------------------------------------------------------------------
    | API Routes - Backend Client (Proxy + Database Access)
    |--------------------------------------------------------------------------
    | 
    | Ce backend agit comme un proxy vers backend-nhs (logique métier)
    | et fournit un accès direct à la base de données locale
    |
    */

    //
    // 🔓 Routes publiques (Health checks et base de données)
    //
    Route::get('/health', function () {
        return response()->json([
            'status' => 'ok',
            'server' => 'Laravel Backend-Client (Proxy)',
            'timestamp' => now(),
            'role' => 'proxy_and_database'
        ]);
    });

    // Upload de logo
    Route::post('/upload-logo', [LogoUploadController::class, 'upload']);

    // Vérification de la base de données locale
    Route::get('/db-check', [DatabaseController::class, 'healthCheck']);

    // Test de connectivité NHS
    Route::get('/nhs-test', [UnifiedProxyController::class, 'testConnection']);


    // 🔧 Personnalisation du site (app-settings)
    use App\Http\Controllers\Api\AppSettingController;
    Route::get('/app-settings', [AppSettingController::class, 'show']);
    Route::put('/app-settings', [AppSettingController::class, 'update']);

    // 🔐 AUTHENTIFICATION - Proxy vers NHS
    Route::post('/login', [UnifiedProxyController::class, 'handle'])->defaults('path', 'login');
    Route::post('/logout', [UnifiedProxyController::class, 'handle'])->defaults('path', 'logout');

    //
    // 🎯 Routes publiques pour invités - Proxy vers NHS
    //
    Route::prefix('guests')->group(function () {
        Route::post('/register', [UnifiedProxyController::class, 'handle'])->defaults('path', 'guests/register');
        Route::post('/login', [UnifiedProxyController::class, 'handle'])->defaults('path', 'guests/login');
    });

    // Route publique pour contacter l'admin - Proxy vers NHS
    Route::post('/contact-admin', [UnifiedProxyController::class, 'handle'])->defaults('path', 'contact-admin');

    // 📁 Route publique pour les avatars
    Route::get('/avatars/{filename}', [AuthController::class, 'getAvatar']); // Avatar local

    //
    // 🔒 Routes protégées par authentification
    //
    Route::middleware('auth:sanctum')->group(function () {
        
        // 👤 Profil utilisateur - Proxy vers NHS
        Route::get('/user', [AuthController::class, 'me']); // Auth locale pour Sanctum
        Route::get('/check', [AuthController::class, 'check']); // Auth locale pour Sanctum
        Route::get('/profile', [UnifiedProxyController::class, 'handle'])->defaults('path', 'profile');
        Route::get('/profile/stats', [UnifiedProxyController::class, 'handle'])->defaults('path', 'profile/stats');
        Route::put('/profile/update', [UnifiedProxyController::class, 'handle'])->defaults('path', 'profile/update');
        Route::post('/profile/verify-password', [UnifiedProxyController::class, 'handle'])->defaults('path', 'profile/verify-password');
        Route::put('/profile/password', [UnifiedProxyController::class, 'handle'])->defaults('path', 'profile/password');
        Route::post('/profile/avatar', [AuthController::class, 'uploadAvatar']);
        Route::delete('/profile/avatar', [AuthController::class, 'deleteAvatar']);

        // 💬 Messages et Conversations - Proxy vers NHS
        Route::get('/conversations', [UnifiedProxyController::class, 'handle'])->defaults('path', 'conversations');
        Route::post('/conversations', [UnifiedProxyController::class, 'handle'])->defaults('path', 'conversations');
        Route::get('/conversations/users', [UnifiedProxyController::class, 'handle'])->defaults('path', 'conversations/users');
        Route::get('/conversations/{groupId}/members', function($groupId, Request $request) {
            return app(UnifiedProxyController::class)->handle($request, 'conversations/' . $groupId . '/members');
        });
        Route::post('/conversations/{groupId}/members', function($groupId, Request $request) {
            return app(UnifiedProxyController::class)->handle($request, 'conversations/' . $groupId . '/members');
        });
        Route::get('/messages/{groupId}', function($groupId, Request $request) {
            return app(UnifiedProxyController::class)->handle($request, 'messages/' . $groupId);
        });
        Route::post('/messages/{groupId}', function($groupId, Request $request) {
            return app(UnifiedProxyController::class)->handle($request, 'messages/' . $groupId);
        });
        Route::post('/conversations/{groupId}/mark-read', function($groupId, Request $request) {
            return app(UnifiedProxyController::class)->handle($request, 'conversations/' . $groupId . '/mark-read');
        });

        // Modération : tous les messages (admin) - Proxy vers NHS
        Route::get('/admin/messages', [UnifiedProxyController::class, 'handle'])->defaults('path', 'admin/messages');
        Route::delete('/admin/conversations/{groupId}', function($groupId, Request $request) {
            return app(UnifiedProxyController::class)->handle($request, 'admin/conversations/' . $groupId);
        });

        // 💬 Rafraîchissement auto - Proxy vers NHS
        Route::get('/conversations/check-changes', [UnifiedProxyController::class, 'handle'])->defaults('path', 'conversations/check-changes');
        Route::get('/messages/{groupId}/check-changes', function($groupId, Request $request) {
            return app(UnifiedProxyController::class)->handle($request, 'messages/' . $groupId . '/check-changes');
        });
        Route::post('/messages/{messageId}/reactions', function($messageId, Request $request) {
            return app(UnifiedProxyController::class)->handle($request, 'messages/' . $messageId . '/reactions');
        });

        // 📁 Fichiers - Accès local + Proxy vers NHS
        Route::get('/files/{fichierId}', [MessageController::class, 'downloadFile']); // Fichier local

        // 👥 Administration des invités - Proxy vers NHS
        Route::prefix('admin/guests')->group(function () {
            Route::get('/', [UnifiedProxyController::class, 'handle'])->defaults('path', 'admin/guests');
            Route::post('/{id}/deactivate', function($id, Request $request) {
                return app(UnifiedProxyController::class)->handle($request, 'admin/guests/' . $id . '/deactivate');
            });
        });
        
        // Gestion des invités temporaires - Proxy vers NHS
        Route::get('/guests', [UnifiedProxyController::class, 'handle'])->defaults('path', 'guests');
        Route::post('/guests/{id}/deactivate', function($id, Request $request) {
            return app(UnifiedProxyController::class)->handle($request, 'guests/' . $id . '/deactivate');
        });

        // 📅 VISITES : Accès direct à la base de données locale (pas de proxy)
        Route::get('/visites', [DatabaseController::class, 'getVisites']);
        Route::post('/visites/{id}/statut', [DatabaseController::class, 'updateVisiteStatus']);
        
        // 📅 VISITES : Création via NHS (logique métier)
        Route::post('/visites', [UnifiedProxyController::class, 'handle'])->defaults('path', 'visites');

        // 🔍 RECHERCHE : Accès direct à la base de données locale pour les recherches
        Route::middleware('nhs.auth')->group(function () {
            Route::get('/residents/search', [DatabaseController::class, 'searchResidents']);
            Route::get('/visitors/search', [DatabaseController::class, 'searchVisitors']);
        });

        // 🏥 Incidents - Proxy vers NHS
        Route::get('/incidents', [UnifiedProxyController::class, 'handle'])->defaults('path', 'incidents');
        Route::post('/incidents', [\App\Http\Controllers\IncidentController::class, 'store']);
        Route::get('/incidents/{id}', function($id, Request $request) {
            return app(UnifiedProxyController::class)->handle($request, 'incidents/' . $id);
        });
        Route::put('/incidents/{id}', function($id, Request $request) {
            return app(UnifiedProxyController::class)->handle($request, 'incidents/' . $id);
        });
        Route::delete('/incidents/{id}', function($id, Request $request) {
            return app(UnifiedProxyController::class)->handle($request, 'incidents/' . $id);
        });

        // 📊 Statistiques admin - Proxy vers NHS
        Route::prefix('admin')->group(function () {
            Route::get('/stats', [UnifiedProxyController::class, 'handle'])->defaults('path', 'admin/stats');
            Route::get('/logs', [UnifiedProxyController::class, 'handle'])->defaults('path', 'admin/logs');
            Route::get('/users', [UnifiedProxyController::class, 'handle'])->defaults('path', 'admin/users');
            Route::post('/users/{id}/ban', function($id, Request $request) {
                return app(UnifiedProxyController::class)->handle($request, 'admin/users/' . $id . '/ban');
            });
            Route::post('/users/{id}/unban', function($id, Request $request) {
                return app(UnifiedProxyController::class)->handle($request, 'admin/users/' . $id . '/unban');
            });
        });

        // Gestion utilisateurs (admin) - Proxy vers NHS
        Route::get('/admin/persons', [UnifiedProxyController::class, 'handle'])->defaults('path', 'admin/persons');
        Route::put('/admin/persons/{id}', function($id, Request $request) {
            return app(UnifiedProxyController::class)->handle($request, 'admin/persons/' . $id);
        });
        Route::post('/admin/persons', [UnifiedProxyController::class, 'handle'])->defaults('path', 'admin/persons');
        Route::delete('/admin/persons/{id}', function($id, Request $request) {
            return app(UnifiedProxyController::class)->handle($request, 'admin/persons/' . $id);
        });

        // Gestion invités (admin) - Proxy vers NHS
        Route::get('/admin/guests', [UnifiedProxyController::class, 'handle'])->defaults('path', 'admin/guests');
        Route::post('/admin/guests', [UnifiedProxyController::class, 'handle'])->defaults('path', 'admin/guests');
        Route::put('/admin/guests/{id}', function($id, Request $request) {
            return app(UnifiedProxyController::class)->handle($request, 'admin/guests/' . $id);
        });
        Route::delete('/admin/guests/{id}', function($id, Request $request) {
            return app(UnifiedProxyController::class)->handle($request, 'admin/guests/' . $id);
        });

        // Incidents admin - Proxy vers NHS
        Route::get('/admin/incidents', [UnifiedProxyController::class, 'handle'])->defaults('path', 'admin/incidents');
        Route::post('/admin/incidents', [\App\Http\Controllers\IncidentController::class, 'store']);
        Route::delete('/admin/incidents/{id}', function($id, Request $request) {
            return app(UnifiedProxyController::class)->handle($request, 'admin/incidents/' . $id);
        });

        // Logs d'activité (admin) - Proxy vers NHS
        Route::post('/admin/logs/delete-before', [UnifiedProxyController::class, 'handle'])->defaults('path', 'admin/logs/delete-before');
    });

    //
    // 🧪 Test API temporaire - Accès direct DB
    //
    Route::get('/test-conversation', [DatabaseController::class, 'testConversation']);

    //
    // 🚀 Routes NHS - Proxy général vers backend-nhs
    //
    Route::prefix('nhs')->group(function () {
        Route::any('/{any}', [UnifiedProxyController::class, 'handle'])->where('any', '.*');
    });

    //
    // 🔄 Proxy catch-all pour toutes les autres routes
    // Redirige automatiquement vers backend-nhs
    //
    Route::fallback(function (Request $request) {
        $path = ltrim($request->path(), 'api/');
        return app(UnifiedProxyController::class)->handle($request, $path);
    });
?>


