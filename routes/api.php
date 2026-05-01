<?php

use Illuminate\Http\Request;
use Illuminate\Support\Facades\Route;
use App\Http\Controllers\Api\LocationController;
use App\Http\Controllers\Api\PropertyController;

Route::middleware(['auth:sanctum'])->get('/user', function (Request $request) {
    return $request->user();
});

// Locations API
Route::get('/governorates', [LocationController::class, 'getGovernorates'])
    ->name('api.governorates.index');

Route::get('/governorates/{id}/districts', [LocationController::class, 'getDistricts'])
    ->name('api.districts.show');

// Properties API
Route::get('/properties/filter-options', [PropertyController::class, 'filterOptions'])->name('api.properties.filter-options');
Route::get('/properties/statistics', [PropertyController::class, 'statistics'])->name('api.properties.statistics');
Route::get('/properties', [PropertyController::class, 'index'])->name('api.properties.index');
Route::get('/properties/{id}', [PropertyController::class, 'show'])->name('api.properties.show');

// Protected routes (require authentication)
// Route::middleware('auth:sanctum')->group(function () {
Route::post('/properties', [PropertyController::class, 'store'])->name('api.properties.store');
Route::put('/properties/{id}', [PropertyController::class, 'update'])->name('api.properties.update');
Route::delete('/properties/{id}', [PropertyController::class, 'destroy'])->name('api.properties.destroy');
// });
