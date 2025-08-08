<?php
// Route proxy NHS : toutes les requêtes NHS passent par ce fichier
use Illuminate\Support\Facades\Route;
use App\Http\Controllers\ProxyNHSController;

Route::prefix('nhs')->group(function () {
    Route::any('/{any}', [ProxyNHSController::class, 'handle'])->where('any', '.*');
});
