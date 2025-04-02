<?php 

namespace	 App\DTO\Appointments;

class CreateAppointmentDTO
{
    public function __construct(
        readonly public string $user_id,
        readonly public string $location_id,
        readonly public string $date_time,
    ){
    }
}