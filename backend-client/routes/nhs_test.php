<?php

use Illuminate\Support\Facades\Route;
use App\Http\Controllers\ProxyNHSController;

// Route de test simple pour NHS
Route::get('/test-proxy', function () {
    return response()->json(['message' => 'Test proxy fonctionne']);
});

// Routes NHS pour le proxy vers backend-nhs
Route::prefix('nhs')->group(function () {
    Route::any('/{any}', [ProxyNHSController::class, 'handle'])->where('any', '.*');
});
