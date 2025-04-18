<?php

use App\Http\Controllers\AppointmentController;
use Illuminate\Support\Facades\Route;
use App\Http\Controllers\TimeSlotController;

Route::get('/appointments/scan', [AppointmentController::class, 'scan'])->name('appointments.scan');

Route::get('/locations/{location}/timeslots', [TimeSlotController::class, 'index'])
    ->name('timeslots.index');

Route::post('/locations/{location}/timeslots', [TimeSlotController::class, 'store'])
    ->name('timeslots.store');

Route::post('/timeslots/{timeSlot}/book', [TimeSlotController::class, 'book'])
    ->name('timeslots.book');

Route::get('/appointments', [AppointmentController::class, 'index'])
    ->name('appointments.index');

Route::delete('/timeslots/{id}', [TimeSlotController::class, 'destroy'])
    ->name('timeslots.destroy');


Route::post('/appointments', [AppointmentController::class, 'store'])
    ->name('appointments.store');

Route::get('/appointments/{id}', [AppointmentController::class, 'create'])
    ->name('appointments.create');

Route::get('/appointments/voucher/{id}', [AppointmentController::class, 'voucher'])
    ->name('appointments.voucher');
