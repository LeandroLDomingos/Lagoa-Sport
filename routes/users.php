<?php

use App\Http\Controllers\UserController;
use Illuminate\Support\Facades\Route;

Route::get(uri: '/users', action: [UserController::class,'index'])->name(name:'users.index');