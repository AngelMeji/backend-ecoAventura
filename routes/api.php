<?php

use Illuminate\Http\Request;
use Illuminate\Support\Facades\Route;
use App\Http\Controllers\Api\AuthController;
use App\Http\Controllers\Api\PlaceController;
use App\Http\Controllers\Api\CategoryController;
use App\Http\Controllers\Api\PasswordResetController;

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

/* ---------- PROTECTED ROUTES ---------- */
Route::middleware(['auth:sanctum'])->group(function () {
    Route::get('/me', [AuthController::class, 'me']);
    Route::post('/logout', [AuthController::class, 'logout']);

    Route::get('/user', function (Request $request) {
        return $request->user();
    });

    // PARTNER REQUEST
    Route::post('/partner-requests', [\App\Http\Controllers\Api\PartnerRequestController::class, 'store']);
    Route::get('/notifications', [\App\Http\Controllers\Api\PartnerRequestController::class, 'getNotifications']);
    Route::patch('/notifications/{id}/read', [\App\Http\Controllers\Api\PartnerRequestController::class, 'markAsRead']);

    // ADMIN DASHBOARD & MANAGEMENT (Solo Admin)
    Route::middleware(['role:admin'])->group(function () {
        Route::get('/admin/dashboard', [\App\Http\Controllers\Api\AdminController::class, 'stats']);

        // ADMIN USER MANAGEMENT
        Route::get('/admin/users', [\App\Http\Controllers\Api\AdminController::class, 'indexUsers']);
        Route::post('/admin/users', [\App\Http\Controllers\Api\AdminController::class, 'createUser']);
        Route::put('/admin/users/{id}', [\App\Http\Controllers\Api\AdminController::class, 'updateUser']);
        Route::delete('/admin/users/{id}', [\App\Http\Controllers\Api\AdminController::class, 'destroyUser']);

        // PARTNER REQUESTS MANAGEMENT
        Route::get('/admin/partner-requests', [\App\Http\Controllers\Api\PartnerRequestController::class, 'index']);
        Route::patch('/admin/partner-requests/{id}/approve', [\App\Http\Controllers\Api\PartnerRequestController::class, 'approve']);
        Route::patch('/admin/partner-requests/{id}/reject', [\App\Http\Controllers\Api\PartnerRequestController::class, 'reject']);
    });
});
