<?php

use App\Models\Invite;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Route;
use App\Http\Controllers\AuthController;
use App\Http\Controllers\MessageController;
use App\Http\Controllers\InviteController;

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

// Routes protégées par authentification
Route::middleware('auth:sanctum')->group(function () {
    // Auth
    Route::post('/logout', [AuthController::class, 'logout']);
    Route::get('/user', [AuthController::class, 'me']);
    Route::get('/check', [AuthController::class, 'check']);
    
    // Messages
    Route::get('/conversations', [MessageController::class, 'getConversations']);
    Route::post('/conversations', [MessageController::class, 'createConversation']);
    Route::get('/conversations/users', [MessageController::class, 'getAvailableUsers']);
    Route::get('/conversations/{groupId}/members', [MessageController::class, 'getGroupMembers']);
    Route::post('/conversations/{groupId}/members', [MessageController::class, 'addGroupMembers']);
    Route::get('/messages/{groupId}', [MessageController::class, 'getMessages']);
    Route::post('/messages/{groupId}', [MessageController::class, 'sendMessage']);
    Route::post('/conversations/{groupId}/mark-read', [MessageController::class, 'markAsRead']);
    
    // Nouvelles routes
    Route::post('/messages/{messageId}/reactions', [MessageController::class, 'addReaction']);
    Route::get('/files/{fichierId}', [MessageController::class, 'downloadFile']);
});
