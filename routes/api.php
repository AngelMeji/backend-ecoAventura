<?php

use Illuminate\Http\Request;
use Illuminate\Support\Facades\Route;
use App\Http\Controllers\Api\AuthController;
use App\Http\Controllers\Api\PlaceController;
use App\Http\Controllers\Api\CategoryController;
use App\Http\Controllers\Api\PasswordResetController;
use App\Http\Controllers\Api\ChatbotController;
use App\Http\Controllers\Api\ReviewController;
use App\Http\Controllers\Api\FavoriteController;
use App\Http\Controllers\Api\ProfileController;
use App\Http\Controllers\Api\PartnerController;
use App\Http\Controllers\Api\UserController;
use App\Http\Controllers\Api\AdminController;
use App\Http\Controllers\Api\PartnerRequestController;

/* ---------- AUTH (Public) ---------- */
Route::post('/register', [AuthController::class, 'register']);
Route::post('/login', [AuthController::class, 'login']);

/* ---------- PASSWORD RESET (Public) ---------- */
Route::post('/password/email', [PasswordResetController::class, 'sendResetLink']);
Route::post('/password/reset', [PasswordResetController::class, 'reset']);

/* ---------- CATEGORÍAS (Public) ---------- */
Route::get('/categories', [CategoryController::class, 'index']);

/* ---------- PLACES (Public) ---------- */
Route::get('/places', [PlaceController::class, 'index']);
Route::get('/places/{id}', [PlaceController::class, 'show']);
Route::post('/places/{id}/chat', [ChatbotController::class, 'chat']);

/* ---------- PROTECTED ROUTES ---------- */
Route::middleware(['auth:sanctum'])->group(function () {

    // AUTH & PROFILE
    Route::get('/me', [AuthController::class, 'me']);
    Route::post('/logout', [AuthController::class, 'logout']);
    Route::put('/me/password', [ProfileController::class, 'updatePassword']);
    Route::match(['put', 'post'], '/me/profile', [ProfileController::class, 'update']);

    Route::get('/user', function (Request $request) {
        return $request->user();
    });

    // ---------- PLACES (CRUD para Admin/Partner) ----------
    Route::post('/places', [PlaceController::class, 'store']);
    Route::put('/places/{id}', [PlaceController::class, 'update']);
    Route::delete('/places/{id}', [PlaceController::class, 'destroy']);
    Route::patch('/places/{id}/set-pending', [PlaceController::class, 'setPending']);
    Route::patch('/places/{id}/approve', [PlaceController::class, 'approve']);
    Route::patch('/places/{id}/reject', [PlaceController::class, 'reject']);
    Route::patch('/places/{id}/needs-fix', [PlaceController::class, 'needsFix']);

    // ---------- REVIEWS ----------
    Route::post('/places/{placeId}/reviews', [ReviewController::class, 'store']);
    Route::put('/reviews/{id}', [ReviewController::class, 'update']);
    Route::delete('/reviews/{id}', [ReviewController::class, 'destroy']);

    // ---------- FAVORITES ----------
    Route::get('/favorites', [FavoriteController::class, 'index']);
    Route::post('/favorites', [FavoriteController::class, 'store']);
    Route::delete('/favorites/{placeId}', [FavoriteController::class, 'destroy']);

    // ---------- PARTNER REQUEST ----------
    Route::post('/partner-requests', [PartnerRequestController::class, 'store']);
    Route::get('/notifications', [PartnerRequestController::class, 'getNotifications']);
    Route::patch('/notifications/{id}/read', [PartnerRequestController::class, 'markAsRead']);

    // ---------- DASHBOARDS ----------
    Route::get('/partner/dashboard', [PartnerController::class, 'dashboard']);
    Route::get('/user/dashboard', [UserController::class, 'dashboard']);

    // ---------- ADMIN ONLY ----------
    Route::middleware(['role:admin'])->group(function () {
        Route::get('/admin/dashboard', [AdminController::class, 'stats']);

        // Admin: Gestión de lugares
        Route::get('/admin/places/pending', [AdminController::class, 'pendingPlaces']);
        Route::get('/admin/places', [AdminController::class, 'allPlaces']);

        // Admin: Gestión de usuarios
        Route::get('/admin/users', [AdminController::class, 'indexUsers']);
        Route::post('/admin/users', [AdminController::class, 'createUser']);
        Route::put('/admin/users/{id}', [AdminController::class, 'updateUser']);
        Route::delete('/admin/users/{id}', [AdminController::class, 'destroyUser']);

        // Admin: Solicitudes de socio
        Route::get('/admin/partner-requests', [PartnerRequestController::class, 'index']);
        Route::patch('/admin/partner-requests/{id}/approve', [PartnerRequestController::class, 'approve']);
        Route::patch('/admin/partner-requests/{id}/reject', [PartnerRequestController::class, 'reject']);
    });
});
