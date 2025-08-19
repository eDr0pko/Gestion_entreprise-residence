<?php

    use Illuminate\Http\Request;
    use Illuminate\Support\Facades\Route;
    use App\Http\Controllers\UnifiedProxyController;
    use App\Http\Controllers\DatabaseController;
    use App\Http\Controllers\AuthController;
    use App\Http\Controllers\MessageController;
    use App\Http\Controllers\Api\LogoUploadController;
    use App\Http\Controllers\Api\BadgeController;

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

    // 🔧 Debug temporaire pour vérifier les données badges
    Route::get('/debug/badges', function () {
        $badges = \App\Models\Badge::with('personne')->get();
        $stats = [
            'total_badges' => \App\Models\Badge::count(),
            'badges_actifs' => \App\Models\Badge::whereExists(function ($query) {
                $query->select(\DB::raw(1))
                      ->from('suivi_badge')
                      ->whereColumn('suivi_badge.numero_badge', 'badge.numero_badge')
                      ->where('suivi_badge.status', 'actif')
                      ->whereNull('suivi_badge.date_fin');
            })->count(),
            'badges_inactifs' => \App\Models\Badge::whereExists(function ($query) {
                $query->select(\DB::raw(1))
                      ->from('suivi_badge')
                      ->whereColumn('suivi_badge.numero_badge', 'badge.numero_badge')
                      ->where('suivi_badge.status', 'inactif')
                      ->whereNull('suivi_badge.date_fin');
            })->count(),
            'badges_suspendus' => \App\Models\Badge::whereExists(function ($query) {
                $query->select(\DB::raw(1))
                      ->from('suivi_badge')
                      ->whereColumn('suivi_badge.numero_badge', 'badge.numero_badge')
                      ->where('suivi_badge.status', 'suspendu')
                      ->whereNull('suivi_badge.date_fin');
            })->count(),
        ];
        
        return response()->json([
            'badges_count' => $badges->count(),
            'badges_data' => $badges->take(5), // Premier 5 badges pour debug
            'stats' => $stats,
            'suivi_count' => \App\Models\SuiviBadge::count(),
            'personne_count' => \App\Models\Personne::count()
        ]);
    });

    // Upload de logo
    Route::post('/upload-logo', [LogoUploadController::class, 'upload']);

    // 🔧 Personnalisation du site (settings)
    use App\Http\Controllers\Api\AppSettingController;
    Route::get('/settings', [AppSettingController::class, 'index']);
    Route::post('/settings', [AppSettingController::class, 'store']);
    Route::post('/settings/upload-logo', [LogoUploadController::class, 'upload']);
    
    // Legacy routes for compatibility
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

    // 🏷️ Gestion des badges (admin & gardien seulement) - PRIORITÉ HAUTE
    Route::middleware(['auth:sanctum', 'role:admin,gardien'])->group(function () {
        Route::get('/badges', [BadgeController::class, 'index']);
        Route::post('/badges', [BadgeController::class, 'store']);
        Route::get('/badges/stats', [BadgeController::class, 'stats']);
        Route::get('/badges/statistiques', [BadgeController::class, 'stats']); // Alias pour compatibilité
        Route::get('/badges/search-users', [BadgeController::class, 'searchUsers']);
        Route::get('/badges/{numero}', [BadgeController::class, 'show']);
        Route::get('/badges/{numero}/activity', [BadgeController::class, 'activity']); // Nouvelle route pour l'activité
        Route::put('/badges/{numero}', [BadgeController::class, 'update']);
        Route::delete('/badges/{numero}', [BadgeController::class, 'destroy']);
        Route::post('/badges/{numero}/toggle-status', [BadgeController::class, 'toggleStatus']);
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




