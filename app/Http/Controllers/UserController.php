<?php

namespace App\Http\Controllers;

use App\Models\User;
use Inertia\Inertia;
use Inertia\Response;


class UserController extends Controller
{
    public function index(): Response
    {
        $users = User::with(['roles','permissions'])->get();
        //dd($users);
        return Inertia::render('users/Index', compact('users'));
    }
}
