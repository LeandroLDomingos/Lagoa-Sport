<?php 

namespace	 App\DTO\Locations;

class CreateLocationDTO
{
    public function __construct(
        readonly public string $name,
        readonly public string $address
    ){
    }
}