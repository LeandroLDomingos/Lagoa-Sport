<?php

namespace App\Http\Controllers;

use App\Models\Appointment;
use App\Models\Location;
use App\Models\Participant;
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
        $slot = TimeSlot::with('location')->findOrFail($id);

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
        $data = $request->validate(
            [
                'timeSlotIds' => 'array',
                'timeSlotIds.*' => 'integer|exists:time_slots,id',
                'participants' => 'required|array|min:1',
                'participants.*.name' => 'required|string|max:255',
                'participants.*.cpf' => 'nullable|string|max:20',
                'participants.*.rg' => 'nullable|string|max:20',
                'participants.*.contact' => 'nullable|string|max:50',
                'participants.*.email' => 'nullable|email|max:255',
            ],
            [
                'participants.required' => 'Você precisa adicionar participantes para agendar.',
            ]
        );

        $timeSlot = TimeSlot::find($data['timeSlotIds'][0]);
        $location = Location::find($timeSlot->location_id);
        $participantIds = [];
        foreach ($data['participants'] as $p) {
            // Tenta achar pelo CPF
            $participant = Participant::firstOrCreate(
                ['cpf' => $p['cpf']],
                $p
            );
            $participantIds[] = $participant->id;
        }

        if (count($data['participants']) < $location->min_participants) {

            $appointment = Appointment::create([
                'user_id' => auth()->user()->id,
                'time_slot_id' => $data['timeSlotIds'][0],
            ]);
            $appointment->participants()->sync($participantIds);
        } else {
            $appointments = [];
            foreach ($data['timeSlotIds'] as $slot_id) {
                $appointments[] = Appointment::create([
                    'user_id' => auth()->user()->id,
                    'time_slot_id' => $slot_id,
                ]);
            }
            $appointments[0]->participants()->sync($participantIds);
        }

        foreach( $data['timeSlotIds'] as $slot_id) {
            $slot = TimeSlot::find($slot_id);
            $slot->update(['is_available' => 0]);
        }


        return to_route('locations.index')
            ->with('flash.success', 'Agendamento criado com sucesso.');
    }


    public function destroy(Appointment $appointment): RedirectResponse
    {
        // Remove participantes e pivot por cascade se configurado
        $appointment->delete();

        return to_route('appointments.index')
            ->with('flash.success', 'Appointment deleted successfully.');
    }

}