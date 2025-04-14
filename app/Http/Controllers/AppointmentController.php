<?php

namespace App\Http\Controllers;

use App\Models\Appointment;
use App\Models\Location;
use App\Models\TimeSlot;
use Inertia\Inertia;
use Inertia\Response;
use Illuminate\Http\Request;
use Illuminate\Http\RedirectResponse;


class AppointmentController extends Controller
{
    public function index(): Response
    {
        return Inertia::render('appointments/Index', [
            'locations' => Location::with("images")->get(),
        ]);
    }

    public function create($id): Response
    {
        $slot = TimeSlot::findOrFail($id);
    
        $nextSlot = TimeSlot::where('location_id', $slot->location_id)
            ->where('date', $slot->date)
            ->where('start_time', $slot->end_time)
            ->where('is_available', true)
            ->first();
    
        return Inertia::render('appointments/Create', [
            'slot' => $slot,
            'canExtendTime' => $nextSlot !== null,
            'nextSlotId' => $nextSlot?->id,
        ]);
    }
    

    public function store(Request $request): RedirectResponse
    {
        $data = $request->validate([
            // campos do appointment...
            'date'              => 'required|date',
            'time_slot_ids'     => 'required|array',
            'time_slot_ids.*'   => 'integer|exists:time_slots,id',
            'participants'             => 'required|array|min:1',
            'participants.*.name'      => 'required|string|max:255',
            'participants.*.cpf'       => 'nullable|string|max:20',
            'participants.*.rg'        => 'nullable|string|max:20',
            'participants.*.contact'   => 'nullable|string|max:50',
            'participants.*.email'     => 'nullable|email|max:255',
        ]);

        // Regra de negócio: até 6 por slot
        $neededSlots = (int) ceil(count($data['participants']) / 6);
        if (count($data['time_slot_ids']) !== $neededSlots) {
            return back()
                ->withErrors([
                    'time_slot_ids' => "You must select {$neededSlots} time slot(s) for ".count($data['participants'])." participants."
                ])
                ->withInput();
        }

        // Cria appointment
        $appointment = Appointment::create([
            'date' => $data['date'],
            // outros campos...
        ]);

        // Relaciona time‑slots
        $appointment->timeSlots()->sync($data['time_slot_ids']);

        // Cria participantes
        foreach ($data['participants'] as $p) {
            $appointment->participants()->create($p);
        }

        return to_route('appointments.index')
            ->with('flash.success', 'Appointment created successfully.');
    }

    public function edit(Appointment $appointment)
    {
        $appointment->load('timeSlots', 'participants');
        $timeSlots = TimeSlot::all();

        return Inertia::render('Appointments/Edit', [
            'appointment' => $appointment,
            'timeSlots'   => $timeSlots,
        ]);
    }

    public function update(Request $request, Appointment $appointment): RedirectResponse
    {
        $data = $request->validate([
            'date'              => 'required|date',
            'time_slot_ids'     => 'required|array',
            'time_slot_ids.*'   => 'integer|exists:time_slots,id',
            'participants'             => 'required|array|min:1',
            'participants.*.name'      => 'required|string|max:255',
            'participants.*.cpf'       => 'nullable|string|max:20',
            'participants.*.rg'        => 'nullable|string|max:20',
            'participants.*.contact'   => 'nullable|string|max:50',
            'participants.*.email'     => 'nullable|email|max:255',
        ]);

        $neededSlots = (int) ceil(count($data['participants']) / 6);
        if (count($data['time_slot_ids']) !== $neededSlots) {
            return back()
                ->withErrors([
                    'time_slot_ids' => "You must select {$neededSlots} time slot(s) for ".count($data['participants'])." participants."
                ])
                ->withInput();
        }

        // Atualiza campos do appointment
        $appointment->update([
            'date' => $data['date'],
            // outros campos...
        ]);

        // Sincroniza time‑slots
        $appointment->timeSlots()->sync($data['time_slot_ids']);

        // Sincroniza participantes: remove todos e recria
        $appointment->participants()->delete();
        foreach ($data['participants'] as $p) {
            $appointment->participants()->create($p);
        }

        return to_route('appointments.index')
            ->with('flash.success', 'Appointment updated successfully.');
    }

    public function destroy(Appointment $appointment): RedirectResponse
    {
        // Remove participantes e pivot por cascade se configurado
        $appointment->delete();

        return to_route('appointments.index')
            ->with('flash.success', 'Appointment deleted successfully.');
    }

}