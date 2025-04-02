<?php 

namespace	 App\DTO\Locations;

class EditLocationDTO
{
    public function __construct(
        readonly public string $id,
        readonly public string $name,
        readonly public string $address
    ){
    }
}