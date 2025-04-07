<?php

use App\Http\Controllers\DocumentController;
use App\Http\Middleware\CheckUserValidation;

Route::middleware(['auth'])->group(function () {
    Route::get('/documents', [DocumentController::class, 'index'])->name('documents.index');
    Route::get('/documents/is_analising', [DocumentController::class, 'is_analising'])->name('documents.is_analising');
    Route::post('/documents', [DocumentController::class, 'store'])->name('documents.store');
    // Route::patch('/documents/{document}', [DocumentController::class, 'update'])->name('documents.update');
    // Route::get('/documents/{document}/download', [DocumentController::class, 'download'])->name('documents.download');
});

Route::middleware(CheckUserValidation::class)->group(function () {
    Route::get('/users/analising', [DocumentController::class, 'analising'])->name('users.analising');
});