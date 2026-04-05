<?php

use Illuminate\Http\Request;
use Illuminate\Support\Facades\Route;
use App\Http\Controllers\Api\AuthController;
use App\Http\Controllers\Api\PlaceController;
use App\Http\Controllers\Api\CategoryController;
use App\Http\Controllers\Api\PasswordResetController;
use App\Http\Controllers\Api\ChatbotController;
use App\Http\Controllers\Api\AdminController;
use App\Http\Controllers\Api\PartnerController;
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

    Route::get('/me', [AuthController::class, 'me']);
    Route::post('/logout', [AuthController::class, 'logout']);

    Route::get('/user', function (Request $request) {
        return $request->user();
    });

    // ---------- FAVORITES (HU006) ----------
    Route::get('/favorites', [\App\Http\Controllers\Api\FavoriteController::class, 'index']);
    Route::post('/favorites', [\App\Http\Controllers\Api\FavoriteController::class, 'store']);
    Route::delete('/favorites/{placeId}', [\App\Http\Controllers\Api\FavoriteController::class, 'destroy']);

    // ---------- USER DASHBOARD (HU006) ----------
    Route::get('/user/dashboard', [\App\Http\Controllers\Api\UserController::class, 'dashboard']);

    // ---------- REVIEWS (HU007) ----------
    Route::get('/reviews', [\App\Http\Controllers\Api\ReviewController::class, 'index']);
    Route::post('/places/{placeId}/reviews', [\App\Http\Controllers\Api\ReviewController::class, 'store']);
    Route::put('/reviews/{id}', [\App\Http\Controllers\Api\ReviewController::class, 'update']);
    Route::delete('/reviews/{id}', [\App\Http\Controllers\Api\ReviewController::class, 'destroy']);

    // ---------- ADMIN/PARTNER PLACES (HU011) ---------- 

    Route::post('/places', [PlaceController::class, 'store']);
    Route::put('/places/{id}', [PlaceController::class, 'update']);
    Route::delete('/places/{id}', [PlaceController::class, 'destroy']);
    Route::patch('/places/{id}/set-pending', [PlaceController::class, 'setPending']);
    Route::patch('/places/{id}/approve', [PlaceController::class, 'approve']);
    Route::patch('/places/{id}/reject', [PlaceController::class, 'reject']);
    Route::patch('/places/{id}/needs-fix', [PlaceController::class, 'needsFix']);

    // PARTNER REQUEST
    Route::post('/partner-requests', [\App\Http\Controllers\Api\PartnerRequestController::class, 'store']);
    Route::get('/notifications', [\App\Http\Controllers\Api\PartnerRequestController::class, 'getNotifications']);
    Route::patch('/notifications/{id}/read', [\App\Http\Controllers\Api\PartnerRequestController::class, 'markAsRead']);

    // ADMIN DASHBOARD & MANAGEMENT (Solo Admin)
    Route::middleware(['role:admin'])->group(function () {
        Route::get('/admin/dashboard', [\App\Http\Controllers\Api\AdminController::class, 'stats']);
        Route::get('/admin/analytics', [\App\Http\Controllers\Api\AdminController::class, 'analytics']);

        // Admin: Gestión de lugares
        Route::get('/admin/places/pending', [\App\Http\Controllers\Api\AdminController::class, 'pendingPlaces']);
        Route::get('/admin/places', [\App\Http\Controllers\Api\AdminController::class, 'allPlaces']);

        // ADMIN USER MANAGEMENT
        Route::get('/admin/users', [\App\Http\Controllers\Api\AdminController::class, 'indexUsers']);
        Route::post('/admin/users', [\App\Http\Controllers\Api\AdminController::class, 'createUser']);
        Route::put('/admin/users/{id}', [\App\Http\Controllers\Api\AdminController::class, 'updateUser']);
        Route::delete('/admin/users/{id}', [\App\Http\Controllers\Api\AdminController::class, 'destroyUser']);

        // PARTNER REQUESTS MANAGEMENT
        Route::get('/admin/partner-requests', [\App\Http\Controllers\Api\PartnerRequestController::class, 'index']);
        Route::patch('/admin/partner-requests/{id}/approve', [\App\Http\Controllers\Api\PartnerRequestController::class, 'approve']);
        Route::patch('/admin/partner-requests/{id}/reject', [\App\Http\Controllers\Api\PartnerRequestController::class, 'reject']);

        // ADMIN REVIEW MODERATION
        Route::get('/admin/reviews', [\App\Http\Controllers\Api\AdminController::class, 'indexReviews']);
        Route::patch('/admin/reviews/{id}/toggle-hide', [\App\Http\Controllers\Api\AdminController::class, 'toggleHideReview']);
    });

    // PARTNER DASHBOARD (Solo Partner)
    Route::middleware(['role:partner'])->group(function () {
        Route::get('/partner/dashboard', [PartnerController::class, 'dashboard']);
    });
});
