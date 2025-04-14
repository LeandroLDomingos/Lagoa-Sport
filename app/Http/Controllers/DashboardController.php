<?php

namespace App\Http\Controllers;

use App\Models\TimeSlot;
use App\Models\User;
use Inertia\Inertia;
use Inertia\Response;
use SebastianBergmann\Type\TrueType;

class DashboardController extends Controller
{
    public function index(): Response
    {
        $pendingUsersCount = User::where('status', 'is_analising')->count();
        $countUsers = User::get()->count();
        $freeSlots = TimeSlot::where('is_available', 1)->count();
        return Inertia::render('Dashboard', [
            'pendingUsersCount' => $pendingUsersCount,
            'users' => $countUsers,
            'freeSlots' => $freeSlots
        ]);
    }
}