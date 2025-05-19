<?php

namespace App\Http\Controllers;

use App\Models\Wine;
use App\Models\UserProfile;
use App\Services\UserProfileService;
use App\Services\WineRecommendationService;
use App\Services\WineDatasetService;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Storage;
use Illuminate\Support\Facades\Validator;
use Illuminate\Support\Facades\Auth;

class WineRecommendationController extends Controller
{
    protected $userProfileService;
    protected $wineRecommendationService;
    protected $wineDatasetService;
    
    public function __construct(
        UserProfileService $userProfileService,
        WineRecommendationService $wineRecommendationService,
        WineDatasetService $wineDatasetService
    ) {
        $this->userProfileService = $userProfileService;
        $this->wineRecommendationService = $wineRecommendationService;
        $this->wineDatasetService = $wineDatasetService;
        
        // Require authentication for all routes
        $this->middleware('auth');
    }
    
    /**
     * Display the recommendation form
     */
    public function index()
    {
        $wineAttributes = $this->wineDatasetService->getWineAttributes();
        
        return view('wine.index', [
            'wineAttributes' => $wineAttributes
        ]);
    }
    
    /**
     * Process the recommendation request
     */
    public function recommend(Request $request)
    {
        // Validate request
        $validator = Validator::make($request->all(), [
            'price_min' => 'nullable|numeric|min:0',
            'price_max' => 'nullable|numeric|gt:price_min',
            'random_mode' => 'nullable|boolean',
        ]);
        
        if ($validator->fails()) {
            return redirect()->back()
                ->withErrors($validator)
                ->withInput();
        }
        
        // Process flavors and food pairings (could be strings with commas)
        $flavors = $request->flavors;
        $foodPairings = $request->food_pairings;
        
        // Get or create user profile using authenticated user
        $user = Auth::user();
        $userProfile = $this->userProfileService->loadUserProfile(null, $user);
        
        // Save preferences to profile
        $userProfile = $this->userProfileService->saveUserProfile($user->name, [
            'flavors' => $flavors ?? [],
            'food_pairings' => $foodPairings ?? [],
            'price_min' => $request->price_min,
            'price_max' => $request->price_max,
            'types' => $request->types ?? [],
            'regions' => $request->regions ?? [],
        ], $user);
        
        // Check if random mode is enabled
        $randomMode = $request->has('random_mode') && $request->random_mode;
        
        // Ensure at least one preference is set (unless in random mode)
        if (!$randomMode && empty($flavors) && empty($foodPairings) && 
            empty($request->types) && empty($request->price_min) && empty($request->price_max)) {
            return redirect()->back()
                ->with('error', 'Please enter at least one preference (e.g., flavor, food pairing, or price range).')
                ->withInput();
        }
        
        // Get recommendations
        $criteria = [
            'flavors' => $flavors ?? [],
            'food_pairings' => $foodPairings ?? [],
            'price_min' => $request->price_min,
            'price_max' => $request->price_max,
            'types' => $request->types ?? [],
            'regions' => $request->regions ?? [],
        ];
        
        $recommendations = $this->wineRecommendationService->getRecommendations($userProfile, $criteria, $randomMode);
        
        // Get past recommendations if available
        $pastRecommendations = $this->userProfileService->getPastRecommendations($userProfile);
        
        return view('wine.recommendations', [
            'recommendations' => $recommendations,
            'pastRecommendations' => $pastRecommendations,
            'userProfile' => $userProfile,
            'criteria' => $criteria,
            'randomMode' => $randomMode
        ]);
    }
    
    /**
     * Rate a wine
     */
    public function rateWine(Request $request)
    {
        $validator = Validator::make($request->all(), [
            'wine_id' => 'required|exists:wines,id',
            'rating' => 'required|integer|min:1|max:5',
            'comment' => 'nullable|string|max:1000',
        ]);
        
        if ($validator->fails()) {
            return response()->json(['error' => $validator->errors()->first()], 400);
        }
        
        $user = Auth::user();
        
        // Log user information for debugging
        \Log::info('Rating wine - User info:', [
            'user_id' => $user->id ?? 'No user ID',
            'user_name' => $user->name ?? 'No name',
            'wine_id' => $request->wine_id,
            'rating' => $request->rating
        ]);
        
        // Ensure user profile exists
        $userProfile = $this->userProfileService->loadUserProfile(null, $user);
        
        if (!$userProfile) {
            // Create profile if it doesn't exist
            $userProfile = $this->userProfileService->saveUserProfile($user->name, [], $user);
            \Log::info('Created new user profile', ['profile_id' => $userProfile->id]);
        }
        
        $rating = $this->userProfileService->rateWine(
            $userProfile,
            $request->wine_id,
            $request->rating,
            $request->comment
        );
        
        // Log successful rating
        \Log::info('Wine rated successfully', [
            'rating_id' => $rating->id,
            'profile_id' => $userProfile->id,
            'wine_id' => $request->wine_id,
            'rating' => $request->rating
        ]);
        
        return response()->json([
            'success' => true, 
            'rating' => $rating,
            'redirect' => route('wine.home')
        ]);
    }
    
    /**
     * Export user profile
     */
    public function exportProfile(Request $request)
    {
        $user = Auth::user();
        $userProfile = $this->userProfileService->loadUserProfile(null, $user);
        
        if (!$userProfile) {
            return redirect()->back()->with('error', 'User profile not found');
        }
        
        $filePath = $this->userProfileService->exportUserProfile($userProfile);
        
        return Storage::download($filePath);
    }
    
    /**
     * Import user profile form
     */
    public function importProfileForm()
    {
        return view('wine.import-profile');
    }
    
    /**
     * Import user profile
     */
    public function importProfile(Request $request)
    {
        $validator = Validator::make($request->all(), [
            'profile_file' => 'required|file|mimes:json',
        ]);
        
        if ($validator->fails()) {
            return redirect()->back()->withErrors($validator);
        }
        
        $file = $request->file('profile_file');
        $path = $file->store('imports');
        
        $user = Auth::user();
        $userProfile = $this->userProfileService->importUserProfile($path, $user);
        
        if (!$userProfile) {
            return redirect()->back()->with('error', 'Failed to import user profile');
        }
        
        return redirect()->route('wine.home')
            ->with('success', 'User profile imported successfully!');
    }
} 