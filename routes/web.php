<?php

use Illuminate\Support\Facades\Route;
use App\Http\Controllers\WineRecommendationController;
use App\Http\Controllers\Admin\WineDatasetController;

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

// Main application routes
Route::get('/', [WineRecommendationController::class, 'index'])->name('wine.index');
Route::post('/recommend', [WineRecommendationController::class, 'recommend'])->name('wine.recommend');
Route::post('/rate', [WineRecommendationController::class, 'rateWine'])->name('wine.rate');
Route::get('/profile/export', [WineRecommendationController::class, 'exportProfile'])->name('wine.profile.export');
Route::get('/profile/import', [WineRecommendationController::class, 'importProfileForm'])->name('wine.profile.import.form');
Route::post('/profile/import', [WineRecommendationController::class, 'importProfile'])->name('wine.profile.import');

// Admin routes for dataset management
Route::prefix('admin')->name('admin.')->group(function () {
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
