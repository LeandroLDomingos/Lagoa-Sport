<?php

use App\Http\Controllers\LocationController;
use Illuminate\Support\Facades\Route;

Route::get('/locations', [LocationController::class, 'index'])->name('locations.index');
Route::get('/locations/create', [LocationController::class, 'create'])->name('locations.create'); 
Route::get('/locations/{id}', [LocationController::class, 'appointment'])->name('locations.appointment'); 
Route::post('/locations/store', [LocationController::class, 'store'])->name('locations.store');