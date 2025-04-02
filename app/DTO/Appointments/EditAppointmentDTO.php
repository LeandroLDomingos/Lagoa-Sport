<?php 

namespace	 App\DTO\Appointments;

class EditAppointmentDTO
{
    public function __construct(
        readonly public string $id,
        readonly public string $name,
        readonly public string $address
    ){
    }
}