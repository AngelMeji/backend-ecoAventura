<?php

namespace App\Http\Controllers\Api;

use App\Http\Controllers\Controller;
use Illuminate\Http\Request;
use App\Models\Review;

class UserController extends Controller
{
    /**
     * Dashboard del Usuario regular
     * Devuelve estadísticas personales: reseñas escritas y favoritos guardados.
     */
    public function dashboard(Request $request)
    {
        $user = $request->user();

        $totalReviews = Review::where('user_id', $user->id)->count();
        $totalFavorites = $user->favorites()->count();

        $stats = [
            'total_reviews'   => $totalReviews,
            'total_favorites' => $totalFavorites,
        ];

        return response()->json([
            'stats' => $stats,
        ]);
    }
}
