<?php

namespace App\Http\Controllers;

use App\Models\Location;
use App\Models\LocationImages;
use App\Models\TimeSlot;
use Carbon\Carbon;
use Illuminate\Http\RedirectResponse;
use Illuminate\Http\Request;
use Inertia\Inertia;
use Inertia\Response;
use Illuminate\Support\Facades\Storage;

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
            'name' => 'required|string|max:255',
            'address' => 'required|string|max:255',
            'images.*' => 'nullable|image|mimes:jpg,png|max:5120', // até 5MB
            'min_participants' => 'required|numeric',
        ]);

        // Cria o location
        $location = Location::create([
            'name' => $data['name'],
            'address' => $data['address'],
            'min_participants' => $data['min_participants'],
        ]);

        // Se houver imagens, armazena e cria registros
        if ($request->hasFile('images')) {
            foreach ($request->file('images') as $file) {
                $image = $file->store("locations/{$location->id}", 'public');
                LocationImages::create([
                    'location_id' => $location->id,
                    'image' => $image,
                ]);
            }
        }

        return to_route('locations.index')
            ->with('flash.success', 'Quadra/Local criado com sucesso!');
    }

    public function appointment($id): Response
    {
        $location = Location::with("images")->findOrFail($id);

        // Data e hora atuais
        $now = Carbon::now();
        $today = $now->toDateString();
        $currentTime = $now->toTimeString();

        // Pega todos os slots liberados (is_available = true) desta semana
        $slots = TimeSlot::with('appointment.user')
            ->where('location_id', $location->id)
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
        // Agrupa por data
        $slotsByDate = $slots->groupBy('date')->map->values();

        // Retorna a view passando os dados da localização
        return Inertia::render('locations/Appointment', [
            'location' => $location,
            'slotsByDate' => $slotsByDate,
        ]);
    }

    public function edit(Location $location)
    {
        // Carrega as imagens relacionadas
        $existingImages = $location->images()->get();

        return Inertia::render('locations/Edit', [
            'location' => $location,
            'existingImages' => $existingImages,
            'canEdit' => auth()->user()->can('update', $location),
        ]);
    }



    public function update(Request $request, Location $location): RedirectResponse
    {
        // dd($request->all());

        // 1. Validação
        $data = $request->validate([
            'name' => 'required|string|max:255',
            'address' => 'required|string|max:255',
            'newImages.*' => 'nullable|image|mimes:jpg,png|max:5120',
            'removedImageIds' => 'nullable|array',
            'removedImageIds.*' => 'integer|exists:location_images,id',
            'min_participants' => 'required|numeric',
        ]);

        // 2. Atualiza nome e endereço
        $location->update([
            'name' => $data['name'],
            'address' => $data['address'],
            'min_participants' => $data['min_participants'],
        ]);

        // 3. Remove imagens marcadas
        if (!empty($data['removedImageIds'])) {
            $toRemove = LocationImages::whereIn('id', $data['removedImageIds'])->get();
            foreach ($toRemove as $img) {
                Storage::disk('public')->delete($img->image);
                $img->delete();
            }
        }

        // 4. Armazena novas imagens
        if ($request->hasFile('newImages')) {
            foreach ($request->file('newImages') as $file) {
                $path = $file->store("locations/{$location->id}", 'public');
                LocationImages::create([
                    'location_id' => $location->id,
                    'image' => $path,
                ]);
            }
        }

        return to_route('locations.index')
            ->with('flash.success', 'Quadra/Local atualizado com sucesso!');
    }

    public function destroy(Location $location): RedirectResponse
    {
        foreach ($location->images as $img) {
            Storage::disk('public')->delete($img->image);
            $img->delete();
        }

        $location->delete();
        return to_route('locations.index')
            ->with('flash.success', 'Local excluído com sucesso!');
    }

}
