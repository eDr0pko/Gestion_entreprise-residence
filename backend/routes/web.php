
<?php
use Illuminate\Support\Facades\Route;

// Fallback pour servir les fichiers de storage/incidents/ en dev (php artisan serve)
Route::get('/storage/incidents/{path}', function ($path) {
    $fullPath = storage_path('app/public/incidents/' . $path);
    if (!file_exists($fullPath)) {
        abort(404);
    }
    return response()->file($fullPath);
})->where('path', '.*');

// Autres routes existantes...


