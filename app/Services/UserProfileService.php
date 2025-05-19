<?php

namespace App\Services;

use App\Models\User;
use App\Models\UserProfile;
use App\Models\WineRating;
use Illuminate\Support\Facades\Storage;

class UserProfileService
{
    /**
     * Create or update a user profile
     *
     * @param string $username
     * @param array $preferences
     * @param User|null $user
     * @return UserProfile
     */
    public function saveUserProfile(string $username, array $preferences, ?User $user = null): UserProfile
    {
        $profile = $user ? $user->profile : null;
        
        if (!$profile) {
            $profile = UserProfile::firstOrNew(['username' => $username]);
            
            // Link to user if provided
            if ($user) {
                $profile->user_id = $user->id;
            }
        }
        
        // Update preferences
        $profile->fill([
            'preferred_flavors' => $preferences['flavors'] ?? [],
            'preferred_food_pairings' => $preferences['food_pairings'] ?? [],
            'preferred_price_min' => $preferences['price_min'] ?? null,
            'preferred_price_max' => $preferences['price_max'] ?? null,
            'preferred_types' => $preferences['types'] ?? [],
            'preferred_regions' => $preferences['regions'] ?? [],
        ]);
        
        $profile->save();
        
        return $profile;
    }
    
    /**
     * Load a user profile by username or user
     *
     * @param string|null $username
     * @param User|null $user
     * @return UserProfile|null
     */
    public function loadUserProfile(?string $username = null, ?User $user = null): ?UserProfile
    {
        if ($user) {
            $profile = $user->profile;
            
            // Create profile if it doesn't exist
            if (!$profile && $user->name) {
                $profile = $this->saveUserProfile($user->name, [], $user);
            }
            
            return $profile;
        }
        
        return $username ? UserProfile::where('username', $username)->first() : null;
    }
    
    /**
     * Save a wine rating
     *
     * @param UserProfile $userProfile
     * @param int $wineId
     * @param int $rating
     * @param string|null $comment
     * @return WineRating
     */
    public function rateWine(UserProfile $userProfile, int $wineId, int $rating, ?string $comment = null): WineRating
    {
        // Rating scale: 1-5 stars
        $rating = max(1, min(5, $rating));
        
        $wineRating = WineRating::updateOrCreate(
            ['user_profile_id' => $userProfile->id, 'wine_id' => $wineId],
            ['rating' => $rating, 'comment' => $comment]
        );
        
        return $wineRating;
    }
    
    /**
     * Get past recommendations for a user
     *
     * @param UserProfile $userProfile
     * @return \Illuminate\Database\Eloquent\Collection
     */
    public function getPastRecommendations(UserProfile $userProfile)
    {
        return $userProfile->pastRecommendations()->with('wine')->get();
    }
    
    /**
     * Export user profile to file (for backup or portability)
     *
     * @param UserProfile $userProfile
     * @return string File path
     */
    public function exportUserProfile(UserProfile $userProfile): string
    {
        $data = [
            'profile' => $userProfile->toArray(),
            'ratings' => $userProfile->wineRatings()->with('wine:id,name')->get()->toArray(),
            'recommendations' => $userProfile->pastRecommendations()->with('wine:id,name')->get()->toArray(),
            'exported_at' => now()->toDateTimeString(),
        ];
        
        $fileName = "user_profile_{$userProfile->username}_" . now()->format('Ymd_His') . '.json';
        $filePath = "exports/{$fileName}";
        
        Storage::put($filePath, json_encode($data, JSON_PRETTY_PRINT));
        
        return $filePath;
    }
    
    /**
     * Import user profile from file
     *
     * @param string $filePath
     * @param User|null $user
     * @return UserProfile|null
     */
    public function importUserProfile(string $filePath, ?User $user = null): ?UserProfile
    {
        if (!Storage::exists($filePath)) {
            return null;
        }
        
        $data = json_decode(Storage::get($filePath), true);
        
        if (!isset($data['profile'])) {
            return null;
        }
        
        $profileData = $data['profile'];
        $profile = $this->saveUserProfile($profileData['username'], [
            'flavors' => $profileData['preferred_flavors'],
            'food_pairings' => $profileData['preferred_food_pairings'],
            'price_min' => $profileData['preferred_price_min'],
            'price_max' => $profileData['preferred_price_max'],
            'types' => $profileData['preferred_types'],
            'regions' => $profileData['preferred_regions'],
        ], $user);
        
        // Optionally restore ratings
        if (isset($data['ratings'])) {
            foreach ($data['ratings'] as $ratingData) {
                $this->rateWine(
                    $profile,
                    $ratingData['wine_id'],
                    $ratingData['rating'],
                    $ratingData['comment'] ?? null
                );
            }
        }
        
        return $profile;
    }
} 