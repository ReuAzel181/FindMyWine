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
            'username' => 'required|string|max:255',
            'price_min' => 'nullable|numeric|min:0',
            'price_max' => 'nullable|numeric|gt:price_min',
            'random_mode' => 'nullable|boolean',
        ]);
        
        if ($validator->fails()) {
            return redirect()->back()
                ->withErrors($validator)
                ->withInput();
        }
        
        // Get or create user profile
        $userProfile = $this->userProfileService->loadUserProfile($request->username);
        
        if (!$userProfile) {
            $userProfile = $this->userProfileService->saveUserProfile($request->username, [
                'flavors' => $request->flavors ?? [],
                'food_pairings' => $request->food_pairings ?? [],
                'price_min' => $request->price_min,
                'price_max' => $request->price_max,
                'types' => $request->types ?? [],
                'regions' => $request->regions ?? [],
            ]);
        }
        
        // Ensure at least one preference is set
        if (empty($request->flavors) && empty($request->food_pairings) && 
            empty($request->types) && empty($request->price_min) && empty($request->price_max)) {
            return redirect()->back()
                ->with('error', 'Please enter at least one preference (e.g., flavor, food pairing, or price range).')
                ->withInput();
        }
        
        // Get recommendations
        $criteria = [
            'flavors' => $request->flavors ?? [],
            'food_pairings' => $request->food_pairings ?? [],
            'price_min' => $request->price_min,
            'price_max' => $request->price_max,
            'types' => $request->types ?? [],
            'regions' => $request->regions ?? [],
        ];
        
        $randomMode = $request->has('random_mode') && $request->random_mode;
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
            'username' => 'required|string|max:255',
            'wine_id' => 'required|exists:wines,id',
            'rating' => 'required|integer|min:1|max:5',
            'comment' => 'nullable|string|max:1000',
        ]);
        
        if ($validator->fails()) {
            return response()->json(['error' => $validator->errors()->first()], 400);
        }
        
        $userProfile = $this->userProfileService->loadUserProfile($request->username);
        
        if (!$userProfile) {
            return response()->json(['error' => 'User profile not found'], 404);
        }
        
        $rating = $this->userProfileService->rateWine(
            $userProfile,
            $request->wine_id,
            $request->rating,
            $request->comment
        );
        
        return response()->json(['success' => true, 'rating' => $rating]);
    }
    
    /**
     * Export user profile
     */
    public function exportProfile(Request $request)
    {
        $validator = Validator::make($request->all(), [
            'username' => 'required|string|max:255',
        ]);
        
        if ($validator->fails()) {
            return redirect()->back()->withErrors($validator);
        }
        
        $userProfile = $this->userProfileService->loadUserProfile($request->username);
        
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
        
        $userProfile = $this->userProfileService->importUserProfile($path);
        
        if (!$userProfile) {
            return redirect()->back()->with('error', 'Failed to import user profile');
        }
        
        return redirect()->route('wine.index')
            ->with('success', 'User profile imported successfully!')
            ->with('username', $userProfile->username);
    }
} 