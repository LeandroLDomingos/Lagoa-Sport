<?php

namespace App\Http\Controllers;

use App\Models\User;
use Inertia\Inertia;
use Inertia\Response;

class DashboardController extends Controller
{
    public function index(): Response
    {
        $pendingUsersCount = User::where('status', 'is_analising')->count();

        return Inertia::render('Dashboard', [
            'pendingUsersCount' => $pendingUsersCount,
        ]);
    }
}