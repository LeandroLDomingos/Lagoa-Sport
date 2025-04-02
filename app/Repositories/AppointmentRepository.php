<?php

namespace App\Repositories;

use App\DTO\Appointments\CreateAppointmentDTO;
use App\DTO\Appointments\EditAppointmentDTO;
use App\Models\Api\Appointment;
use Carbon\Carbon;
use Illuminate\Database\Eloquent\Collection;
use Illuminate\Pagination\LengthAwarePaginator;

class AppointmentRepository
{
    public function __construct(protected Appointment $appointment)
    {
    }

    public function getPaginate(int $totalPerPage = 15, int $page = 1, string $filter = ''): LengthAwarePaginator
    {
        return $this->appointment->where(function ($query) use ($filter) {
            if ($filter !== '') {
                $query->where('name', 'LIKE', "%{$filter}%");
            }
        })
        ->with('user','location')
        ->paginate($totalPerPage, ['*'], 'page', $page);
    }

    public function createNew(CreateAppointmentDTO $dto): Appointment
    {
        if (!$this->isValidTimeSlot($dto->date_time)) {
            throw new \InvalidArgumentException("O horário do agendamento deve estar entre 08:00 e 20:00, e ser exato na hora cheia.");
        }

        if ($this->appointmentExists($dto->location_id, $dto->date_time)) {
            throw new \InvalidArgumentException("Já existe um agendamento para este local e horário.");
        }

        if ($this->userHasAppointmentOnDate($dto->user_id, $dto->date_time)) {
            throw new \InvalidArgumentException("Você já possui um agendamento para este dia.");
        }

        return $this->appointment->create((array) $dto);
    }

    public function findById(string $id, array $relations = []): ?Appointment
    {
        return $this->appointment->with($relations)->find($id);
    }

    public function update(EditAppointmentDTO $dto): bool
    {
        if (!$appointment = $this->findById($dto->id)) {
            return false;
        }
        return $appointment->update((array) $dto);
    }

    public function delete(string $id): bool
    {
        if (!$appointment = $this->findById($id)) {
            return false;
        }
        return $appointment->delete();
    }

    private function isValidTimeSlot(string $dateTime): bool
    {
        $appointmentTime = Carbon::parse($dateTime);
        return $appointmentTime->hour >= 8 && $appointmentTime->hour <= 20 && $appointmentTime->minute == 0;
    }

    private function appointmentExists(int $locationId, string $dateTime): bool
    {
        return $this->appointment
            ->where('location_id', $locationId)
            ->where('date_time', $dateTime)
            ->exists();
    }
    
    private function userHasAppointmentOnDate(string $userId, string $dateTime): bool
    {
        $date = Carbon::parse($dateTime)->toDateString(); // Obtém apenas a data (YYYY-MM-DD)

        return $this->appointment
            ->where('user_id', $userId)
            ->whereDate('date_time', $date) // Compara apenas a data, ignorando o horário
            ->exists();
    }
}