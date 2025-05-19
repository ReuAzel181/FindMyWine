<?php

namespace App\Services;

use App\Models\Wine;
use App\Models\UserProfile;
use App\Models\WineRating;
use App\Models\UserRecommendation;
use Illuminate\Support\Collection;
use Illuminate\Support\Facades\DB;

class WineRecommendationService
{
    /**
     * Get 3-5 wine recommendations based on user preferences
     *
     * @param UserProfile $userProfile
     * @param array $criteria
     * @param bool $randomMode
     * @return Collection
     */
    public function getRecommendations(UserProfile $userProfile, array $criteria, bool $randomMode = false): Collection
    {
        // Start building the query
        $query = Wine::query();
        
        if ($randomMode) {
            // In random mode, only consider price range if specified
            if (!empty($criteria['price_min'])) {
                $query->where('price', '>=', $criteria['price_min']);
            }
            
            if (!empty($criteria['price_max'])) {
                $query->where('price', '<=', $criteria['price_max']);
            }
            
            // Get 6 random wines
            return $query->inRandomOrder()->take(6)->get();
        }
        
        // Apply criteria filters
        $this->applyCriteriaFilters($query, $criteria);
        
        // Consider user ratings for personalization
        $highlyRatedWines = $this->getHighlyRatedWineIds($userProfile);
        
        // Get recommendations, prioritizing highly rated wines if available
        $recommendations = new Collection();
        
        if ($highlyRatedWines->isNotEmpty()) {
            // First get wines similar to highly rated ones
            $similarWines = $this->getSimilarWines($highlyRatedWines, $criteria);
            $recommendations = $similarWines->take(2);
            
            // If we have fewer than 3 recommendations, get more with regular criteria
            if ($recommendations->count() < 3) {
                $additionalWines = $query->whereNotIn('id', $recommendations->pluck('id'))
                    ->inRandomOrder()
                    ->take(6 - $recommendations->count())
                    ->get();
                
                $recommendations = $recommendations->merge($additionalWines);
            }
        } else {
            // No ratings, just get 6 wines based on criteria
            $recommendations = $query->inRandomOrder()->take(6)->get();
        }
        
        // Save these recommendations for the user
        $this->saveRecommendations($userProfile, $recommendations, $criteria);
        
        return $recommendations;
    }
    
    /**
     * Apply specified criteria to the wine query
     *
     * @param \Illuminate\Database\Eloquent\Builder $query
     * @param array $criteria
     * @return void
     */
    private function applyCriteriaFilters($query, array $criteria): void
    {
        // Apply price range filter
        if (!empty($criteria['price_min'])) {
            $query->where('price', '>=', $criteria['price_min']);
        }
        
        if (!empty($criteria['price_max'])) {
            $query->where('price', '<=', $criteria['price_max']);
        }
        
        // Apply wine type filter
        if (!empty($criteria['types'])) {
            $query->whereIn('type', $criteria['types']);
        }
        
        // Apply flavor profile filter (using LIKE for partial matching)
        if (!empty($criteria['flavors'])) {
            $query->where(function ($q) use ($criteria) {
                // Convert string to array if it's not already an array
                $flavors = is_array($criteria['flavors']) ? $criteria['flavors'] : explode(',', $criteria['flavors']);
                
                foreach ($flavors as $flavor) {
                    $flavor = trim($flavor);
                    if (!empty($flavor)) {
                        $q->orWhere('flavor_profile', 'LIKE', "%{$flavor}%");
                        // Also search for similar terms
                        if ($flavor === 'oaky') {
                            $q->orWhere('flavor_profile', 'LIKE', '%oak%');
                        }
                    }
                }
            });
        }
        
        // Apply food pairing filter (using LIKE for partial matching)
        if (!empty($criteria['food_pairings'])) {
            $query->where(function ($q) use ($criteria) {
                // Convert string to array if it's not already an array
                $pairings = is_array($criteria['food_pairings']) ? $criteria['food_pairings'] : explode(',', $criteria['food_pairings']);
                
                foreach ($pairings as $pairing) {
                    $pairing = trim($pairing);
                    if (!empty($pairing)) {
                        $q->orWhere('food_pairings', 'LIKE', "%{$pairing}%");
                        // Add common variations
                        if ($pairing === 'fish') {
                            $q->orWhere('food_pairings', 'LIKE', '%seafood%');
                        }
                    }
                }
            });
        }
        
        // Apply region filter
        if (!empty($criteria['regions'])) {
            $query->whereIn('region', $criteria['regions']);
        }
    }
    
    /**
     * Get IDs of wines that the user has rated highly (4-5 stars)
     *
     * @param UserProfile $userProfile
     * @return Collection
     */
    private function getHighlyRatedWineIds(UserProfile $userProfile): Collection
    {
        return WineRating::where('user_profile_id', $userProfile->id)
            ->where('rating', '>=', 4)
            ->pluck('wine_id');
    }
    
    /**
     * Get wines similar to the ones the user has rated highly
     *
     * @param Collection $highlyRatedWineIds
     * @param array $criteria
     * @return Collection
     */
    private function getSimilarWines(Collection $highlyRatedWineIds, array $criteria): Collection
    {
        // Get attributes of highly rated wines
        $highlyRatedWines = Wine::whereIn('id', $highlyRatedWineIds)->get();
        
        // Extract common attributes to look for
        $commonTypes = $highlyRatedWines->pluck('type')->unique();
        $commonRegions = $highlyRatedWines->pluck('region')->unique();
        $commonGrapes = $highlyRatedWines->pluck('grape_variety')->unique();
        
        // Build query for similar wines
        $query = Wine::query();
        
        // Don't recommend the same wines again
        $query->whereNotIn('id', $highlyRatedWineIds);
        
        // Apply price criteria
        if (!empty($criteria['price_min'])) {
            $query->where('price', '>=', $criteria['price_min']);
        }
        
        if (!empty($criteria['price_max'])) {
            $query->where('price', '<=', $criteria['price_max']);
        }
        
        // Apply similarity criteria
        $query->where(function ($q) use ($commonTypes, $commonRegions, $commonGrapes) {
            if ($commonTypes->isNotEmpty()) {
                $q->orWhereIn('type', $commonTypes);
            }
            
            if ($commonRegions->isNotEmpty()) {
                $q->orWhereIn('region', $commonRegions);
            }
            
            if ($commonGrapes->isNotEmpty()) {
                $q->orWhereIn('grape_variety', $commonGrapes);
            }
        });
        
        return $query->inRandomOrder()->take(5)->get();
    }
    
    /**
     * Save recommendations to user history
     *
     * @param UserProfile $userProfile
     * @param Collection $recommendations
     * @param array $criteria
     * @return void
     */
    private function saveRecommendations(UserProfile $userProfile, Collection $recommendations, array $criteria): void
    {
        foreach ($recommendations as $wine) {
            UserRecommendation::create([
                'user_profile_id' => $userProfile->id,
                'wine_id' => $wine->id,
                'recommended_at' => now(),
                'criteria_used' => $criteria,
            ]);
        }
    }
} 