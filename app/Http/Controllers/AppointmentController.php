<?php

namespace App\Http\Controllers;

use App\Models\Location;
use Inertia\Inertia;
use Inertia\Response;

class AppointmentController extends Controller
{
    public function index(): Response
    {
        return Inertia::render('appointments/Index', [
            'locations' => Location::with("images")->get(),
        ]);
    }
}