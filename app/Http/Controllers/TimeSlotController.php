<?php

namespace App\Http\Controllers;

use App\Models\TimeSlot;
use App\Models\Location;
use Illuminate\Http\Request;
use Inertia\Inertia;
use Carbon\Carbon;
use Carbon\CarbonPeriod;

class TimeSlotController extends Controller
{
    public function index(Request $request, Location $location)
    {
        $startOfWeek = $request->input('week_start')
            ? Carbon::parse($request->input('week_start'))->startOfWeek(Carbon::MONDAY)
            : now()->startOfWeek(Carbon::MONDAY);

        $endOfWeek = (clone $startOfWeek)->endOfWeek(Carbon::SUNDAY);
        $period = CarbonPeriod::create($startOfWeek, $endOfWeek);
        $defaultHours = range(8, 21); // 08h até 20h (último termina às 21h)

        $allSlots = [];

        foreach ($period as $date) {
            foreach ($defaultHours as $hour) {
                $startTime = sprintf('%02d:00:00', $hour);
                $endTime = sprintf('%02d:00:00', $hour + 1);

                // Verifica se o horário é no futuro
                $slotDateTime = Carbon::parse($date->toDateString() . ' ' . $startTime);
                if ($slotDateTime->isPast()) {
                    continue; // Ignora horários no passado
                }

                // Verifica se já existe um slot no banco de dados
                $existingSlot = TimeSlot::with('appointment')
                    ->where('location_id', $location->id)
                    ->where('date', $date->toDateString())
                    ->where('start_time', $startTime)
                    ->where('end_time', $endTime)
                    ->first();
                if ($existingSlot) {
                    $allSlots[] = $existingSlot;
                } else {
                    // Slot ainda não existe, mas queremos exibir como indisponível
                    $allSlots[] = new TimeSlot([
                        'id' => null,
                        'location_id' => $location->id,
                        'date' => $date->toDateString(),
                        'start_time' => $startTime,
                        'end_time' => $endTime,
                        'is_available' => false,
                    ]);
                }
            }
        }


        $weekOptions = collect(range(0, 3))->map(function ($weekOffset) {
            $start = now()->startOfWeek(Carbon::MONDAY)->addWeeks($weekOffset);
            $end = (clone $start)->endOfWeek(weekEndsAt: Carbon::SUNDAY);
            return [
                'label' => $start->format('d/m') . ' a ' . $end->format('d/m'),
                'value' => $start->toDateString()
            ];
        });

        return Inertia::render('timeslots/Index', [
            'location' => $location,
            'timeSlots' => $allSlots,
            'weekStart' => $startOfWeek->toDateString(),
            'weekEnd' => $endOfWeek->toDateString(),
            'weekOptions' => $weekOptions,
        ]);
    }

    public function store(Request $request, Location $location)
    {
        // Validação de um array de slots
        $data = $request->validate([
            'slots' => 'required|array',
            'slots.*.date' => 'required|date',
            'slots.*.start_time' => 'required|date_format:H:i:s',
            'slots.*.end_time' => 'required|date_format:H:i:s|after:slots.*.start_time',
        ]);

        // Processar cada slot
        foreach ($data['slots'] as $slotData) {
            TimeSlot::firstOrCreate([
                'location_id' => $location->id,
                'date' => $slotData['date'],
                'start_time' => $slotData['start_time'], // Já vem com :00, então não é necessário concatenar
                'end_time' => $slotData['end_time'], // O mesmo vale para o end_time
            ], [
                'is_available' => true,
            ]);
        }

        return to_route('appointments.index')
            ->with('flash.success', 'Horários liberados com sucesso!');
    }

    public function destroy($id)
    {
        $slot = TimeSlot::findOrFail($id);
        if (!$slot->is_available) {
            return redirect()->back()->withErrors('Não é possível excluir um horário que já foi reservado.');
        }

        $slot->delete();

        return to_route('appointments.index')
            ->with('flash.success', 'Horário excluido com sucesso!');
    }



}
