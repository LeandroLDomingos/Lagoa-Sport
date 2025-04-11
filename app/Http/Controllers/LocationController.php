<?php

namespace App\Http\Controllers;

use App\Models\Location;
use App\Models\LocationImages;
use Illuminate\Http\RedirectResponse;
use Illuminate\Http\Request;
use Inertia\Inertia;
use Inertia\Response;

class LocationController extends Controller
{
    public function index(): Response
    {
        $locations = Location::with("images")->get();
        return Inertia::render('locations/Index', compact('locations'));
    }

    public function create(): Response
    {
        return Inertia::render('locations/Create');
    }

    public function store(Request $request): RedirectResponse
    {
        $data = $request->validate([
            'name'        => 'required|string|max:255',
            'address' => 'required|string|max:255',
            'images.*'    => 'nullable|image|mimes:jpg,png|max:5120', // até 5MB
        ]);

        // Cria o location
        $location = Location::create([
            'name'        => $data['name'],
            'address' => $data['address'] ,
        ]);

        // Se houver imagens, armazena e cria registros
        if ($request->hasFile('images')) {
            foreach ($request->file('images') as $file) {
                $image = $file->store("locations/{$location->id}", 'public');
                LocationImages::create([
                    'location_id' => $location->id,
                    'image'        => $image,
                ]);
            }
        }

        return to_route('locations.index')
            ->with('flash.success', 'Quadra/Local criado com sucesso!');
    }

    public function appointment($id): Response
    {
        $location = Location::with("images")->findOrFail($id);

        // Retorna a view passando os dados da localização
        return Inertia::render('locations/Appointment', compact('location'));
    }

}
