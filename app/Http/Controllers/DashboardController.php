<?php

namespace App\Http\Controllers;

use App\Models\TimeSlot;
use App\Models\User;
use Carbon\Carbon;
use Inertia\Inertia;
use Inertia\Response;
use SebastianBergmann\Type\TrueType;

class DashboardController extends Controller
{
    public function index(): Response
    {
        $pendingUsersCount = User::where('status', 'is_analising')->count();
        $countUsers = User::get()->count();

        // Data e hora atuais
        $now = Carbon::now();
        $today = $now->toDateString();
        $currentTime = $now->toTimeString();

        // Pega todos os slots liberados (is_available = true) desta semana
        $slots = TimeSlot::with('appointment.user')
            ->where('is_available', 1)
            ->whereBetween('date', [
                now()->startOfWeek(),
                now()->endOfWeek()
            ])
            ->where(function ($query) use ($today, $currentTime) {
                $query->where('date', '>', $today)
                    ->orWhere(function ($query) use ($today, $currentTime) {
                        $query->where('date', $today)
                            ->where('start_time', '>', $currentTime);
                    });
            })

            ->orderBy('date')
            ->orderBy('start_time')
            ->get();

        $freeSlots = $slots->count();
        return Inertia::render('Dashboard', [
            'pendingUsersCount' => $pendingUsersCount,
            'users' => $countUsers,
            'freeSlots' => $freeSlots
        ]);
    }
}