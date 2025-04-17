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
    
        // Obtém o primeiro timeSlot
        $firstSlot = TimeSlot::findOrFail($data['timeSlotIds'][0]);
        $location = $firstSlot->location;
        
        // Cria ou obtém os participantes
        $participantIds = collect($data['participants'])->map(function ($p) {
            return Participant::updateOrCreate(
                ['cpf' => $p['cpf']],  // Atualiza ou cria o participante com base no CPF
                $p  // Atribui os outros dados como name, rg, etc.
            )->id;
        });
    
        // Criação do agendamento
        $appointment = Appointment::create([
            'user_id' => auth()->id(),
        ]);
    
        // Verifica se o número de participantes é suficiente para dois time slots
        if (count($participantIds) >= $location->min_participants) {
            // Se o número de participantes for suficiente, associa os participantes e 2 timeSlots
            $appointment->participants()->sync($participantIds);
            // Associa os dois time slots ao agendamento
            $appointment->timeSlots()->sync($data['timeSlotIds']);
        } else {
            // Caso contrário, associa apenas 1 timeSlot
            $appointment->participants()->sync($participantIds);
            $appointment->timeSlots()->sync([$data['timeSlotIds'][0]]);
        }
    
        // Marca os time slots como indisponíveis
        TimeSlot::whereIn('id', $data['timeSlotIds'])->update(['is_available' => false]);
    
        // Redireciona para o voucher do agendamento
        return redirect()->to(route('appointments.voucher', ['id' => $appointment->id]))
            ->with('flash.success', 'Agendado com sucesso.');
    }
    
    public function voucher($id)
    {
        $appointment = Appointment::with(['participants', 'timeSlots.location','user'])->find($id);
        return Inertia::render('appointments/Voucher', [
            'appointment' => $appointment,
        ]);
    }
    

    public function destroy(Appointment $appointment): RedirectResponse
    {
        // Remove participantes e pivot por cascade se configurado
        $appointment->delete();

        return to_route('appointments.index')
            ->with('flash.success', 'Appointment deleted successfully.');
    }

    public function scan()
    {
        return Inertia::render('appointments/Scan');
    }
    
}