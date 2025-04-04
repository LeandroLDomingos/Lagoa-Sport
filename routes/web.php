<?php

use App\Http\Middleware\ACLMiddleware;
use App\Http\Middleware\CheckUserValidation;
use Illuminate\Support\Facades\Route;
use Inertia\Inertia;

Route::get('/', function () {
    return Inertia::render('Welcome');
})->name('home');

Route::middleware([CheckUserValidation::class])->group(function () {

    Route::get('dashboard', function () {
        return Inertia::render('Dashboard');
    })->middleware(['auth', 'verified'])->name('dashboard');

    require __DIR__ . '/settings.php';
    require __DIR__ . '/auth.php';
    
});
require __DIR__ . '/documents.php';