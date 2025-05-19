<?php

namespace App\Http\Controllers;

use App\Models\User;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\Hash;

class UserController extends Controller
{
    /**
     * Create a new controller instance.
     *
     * @return void
     */
    public function __construct()
    {
        // Only apply auth middleware to these methods, not to deleteFromLogin
        $this->middleware('auth')->except(['deleteFromLogin']);
    }

    /**
     * Show the user account settings.
     *
     * @return \Illuminate\View\View
     */
    public function accountSettings()
    {
        return view('user.account-settings');
    }

    /**
     * Show the delete account confirmation page.
     *
     * @return \Illuminate\View\View
     */
    public function showDeleteAccount()
    {
        return view('user.delete-account');
    }

    /**
     * Delete the user's account.
     *
     * @param  \Illuminate\Http\Request  $request
     * @return \Illuminate\Http\RedirectResponse
     */
    public function deleteAccount(Request $request)
    {
        // Validate password to confirm account deletion
        $request->validate([
            'password' => 'required',
        ]);
        
        $user = Auth::user();
        
        // Check if password is correct
        if (!Hash::check($request->password, $user->password)) {
            return back()->withErrors([
                'password' => 'The provided password is incorrect.',
            ]);
        }
        
        // Log the user out
        Auth::logout();
        
        // Delete the user
        $user->delete();
        
        // Clear session
        $request->session()->invalidate();
        $request->session()->regenerateToken();
        
        // Redirect to homepage with success message
        return redirect('/')->with('success', 'Your account has been permanently deleted.');
    }
    
    /**
     * Delete a user account from the login page.
     * 
     * @param \Illuminate\Http\Request $request
     * @return \Illuminate\Http\RedirectResponse
     */
    public function deleteFromLogin(Request $request)
    {
        // Validate the request
        $request->validate([
            'username' => 'required',
        ]);
        
        // Find the user by username
        $user = User::where('username', $request->username)->first();
        
        if ($user) {
            // Get user ID before deletion for audit purposes
            $userId = $user->id;
            $userName = $user->name;
            
            // Delete associated profile data first to prevent foreign key constraint issues
            if ($user->profile) {
                // Delete associated data
                $user->profile->wineRatings()->delete();
                $user->profile->pastRecommendations()->delete();
                $user->profile->delete();
            }
            
            // Delete the user
            $user->delete();
            
            // Log the deletion
            \Log::info("User deleted from login page: {$userId} - {$userName}");
            
            return redirect()->route('login')->with('success', 'Account has been permanently deleted.');
        }
        
        return redirect()->route('login')->with('error', 'Account not found.');
    }
} 