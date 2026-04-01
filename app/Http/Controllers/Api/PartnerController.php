<?php

namespace App\Http\Controllers\Api;

use App\Http\Controllers\Controller;
use Illuminate\Http\Request;
use App\Models\Place;
use App\Models\Review;
use Illuminate\Support\Facades\Cache;

class PartnerController extends Controller
{
    /**
     * Dashboard del Socio (Mis Publicaciones y Estadísticas Personales)
     */
    public function dashboard(Request $request)
    {
        $user = $request->user();

        // 1. Estadísticas básicas (sólo del socio)
        $stats = Cache::remember("partner_stats_{$user->id}", 60, function() use ($user) {
            $placesQuery = Place::where('user_id', $user->id);
            
            $totalPlaces = (clone $placesQuery)->count();
            $approvedPlaces = (clone $placesQuery)->where('status', 'approved')->count();
            $pendingPlaces = (clone $placesQuery)->where('status', 'pending')->count();
            
            $totalReviews = Review::whereHas('place', function($q) use ($user) {
                $q->where('user_id', $user->id);
            })->count();

            $averageRating = Place::where('user_id', $user->id)
                ->join('reviews', 'places.id', '=', 'reviews.place_id')
                ->avg('reviews.rating');

            return [
                'total_places' => $totalPlaces,
                'approved_places' => $approvedPlaces,
                'pending_places' => $pendingPlaces,
                'total_reviews' => $totalReviews,
                'average_rating' => round($averageRating ?? 0, 1)
            ];
        });

        // 2. Obtener lugares del socio (Mis Publicaciones)
        $recent_places = Place::where('user_id', $user->id)
            ->with(['category', 'images'])
            ->withAvg('reviews', 'rating')
            ->latest()
            ->get();

        return response()->json([
            'stats' => $stats,
            'recent_places' => $recent_places
        ]);
    }
}
