<?php

use App\Http\Controllers\DocumentController;

Route::middleware(['auth'])->group(function () {
    Route::get('/documents', [DocumentController::class, 'index'])->name('documents.index');
    Route::post('/documents', [DocumentController::class, 'store'])->name('documents.store');
    Route::patch('/documents/{document}', [DocumentController::class, 'update'])->name('documents.update');
    Route::get('/documents/{document}/download', [DocumentController::class, 'download'])->name('documents.download');
});