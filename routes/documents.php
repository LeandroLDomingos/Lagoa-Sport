<?php

use App\Http\Controllers\DocumentController;
use App\Http\Middleware\ACLMiddleware;
use App\Http\Middleware\CheckUserValidation;

Route::middleware(CheckUserValidation::class)->group(function () {
    Route::get('/documents', [DocumentController::class, 'index'])->name('documents.index');
    Route::get('/documents/is_analising', [DocumentController::class, 'is_analising'])->name('documents.is_analising');
    Route::post('/documents', [DocumentController::class, 'store'])->name('documents.store');
    Route::middleware(ACLMiddleware::class)->group(function () {
        Route::get('/users/analising', [DocumentController::class, 'analising'])->name('users.analising');
        Route::post('/users/{user}/approve', [DocumentController::class, 'user_approve'])->name('users.approve');
        Route::post('/documents/{document}/approve', [DocumentController::class, 'approve'])->name('documents.approve');
        Route::post('/documents/{document}/reject', [DocumentController::class, 'reject'])->name('documents.reject');

    });
});