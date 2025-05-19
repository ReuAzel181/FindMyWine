<?php

use Illuminate\Support\Facades\Route;
use Illuminate\Support\Facades\Auth;
use App\Http\Controllers\WineRecommendationController;
use App\Http\Controllers\Admin\WineDatasetController;
use App\Http\Controllers\HomeController;
use App\Http\Controllers\UserController;

/*
|--------------------------------------------------------------------------
| Web Routes
|--------------------------------------------------------------------------
|
| Here is where you can register web routes for your application. These
| routes are loaded by the RouteServiceProvider and all of them will
| be assigned to the "web" middleware group. Make something great!
|
*/

// Authentication routes
Auth::routes();

// Redirect root to the login page if not authenticated
Route::get('/', function () {
    return Auth::check() ? redirect()->route('wine.home') : redirect()->route('login');
})->name('root');

// Main application routes (all protected by auth)
Route::middleware(['auth'])->group(function () {
    Route::get('/wine', [WineRecommendationController::class, 'index'])->name('wine.home');
    Route::post('/recommend', [WineRecommendationController::class, 'recommend'])->name('wine.recommend');
    Route::post('/rate', [WineRecommendationController::class, 'rateWine'])->name('wine.rate');
    Route::get('/profile/export', [WineRecommendationController::class, 'exportProfile'])->name('wine.profile.export');
    Route::get('/profile/import', [WineRecommendationController::class, 'importProfileForm'])->name('wine.profile.import.form');
    Route::post('/profile/import', [WineRecommendationController::class, 'importProfile'])->name('wine.profile.import');
    
    // Redirect /home to the wine homepage
    Route::get('/home', [HomeController::class, 'index'])->name('home');
});

// Admin routes for dataset management
Route::prefix('admin')->name('admin.')->middleware(['auth'])->group(function () {
    Route::get('/dataset', [WineDatasetController::class, 'index'])->name('dataset.index');
    Route::get('/dataset/import', [WineDatasetController::class, 'importForm'])->name('dataset.import.form');
    Route::post('/dataset/import', [WineDatasetController::class, 'importWines'])->name('dataset.import');
    Route::get('/dataset/export', [WineDatasetController::class, 'exportWines'])->name('dataset.export');
    Route::get('/dataset/wines', [WineDatasetController::class, 'listWines'])->name('dataset.list');
    Route::get('/dataset/wines/create', [WineDatasetController::class, 'createWine'])->name('dataset.create');
    Route::post('/dataset/wines', [WineDatasetController::class, 'storeWine'])->name('dataset.store');
    Route::get('/dataset/wines/{id}/edit', [WineDatasetController::class, 'editWine'])->name('dataset.edit');
    Route::put('/dataset/wines/{id}', [WineDatasetController::class, 'updateWine'])->name('dataset.update');
    Route::delete('/dataset/wines/{id}', [WineDatasetController::class, 'deleteWine'])->name('dataset.delete');
});

// Route for running artisan commands through web interface (admin only)
Route::get('/artisan/command/{command}', function($command) {
    // Ensure user is authenticated
    if (!Auth::check()) {
        return redirect()->route('login');
    }
    
    // Only allow specific commands
    $allowedCommands = [
        'seed:additional-wines',
    ];
    
    if (!in_array($command, $allowedCommands)) {
        return redirect()->back()->with('error', 'Command not allowed.');
    }
    
    // Run the command
    try {
        \Artisan::call($command);
        $output = \Artisan::output();
        return redirect()->back()->with('success', "Command executed successfully: {$output}");
    } catch (\Exception $e) {
        return redirect()->back()->with('error', "Error executing command: {$e->getMessage()}");
    }
})->middleware('auth');

// User account settings and deletion routes
Route::get('/account/settings', [UserController::class, 'accountSettings'])->name('user.account-settings');
Route::get('/account/delete', [UserController::class, 'showDeleteAccount'])->name('user.delete-account');
Route::post('/account/delete', [UserController::class, 'deleteAccount']);

// Delete account from login page (no auth required)
Route::post('/account/delete-from-login', [UserController::class, 'deleteFromLogin'])->name('user.delete-from-login');
