<?php 

namespace	 App\DTO\Users;

class CreateUserDTO
{
    public function __construct(
        readonly public string $name,
        readonly public string $last_name,
        readonly public string $email,
        readonly public string $password,
        readonly public ?string $cpf = null,
        readonly public ?string $zip_code = null,
        readonly public ?string $address = null,
        readonly public ?string $neighborhood = null,
        readonly public ?string $number = null,
        readonly public ?string $complement = null,
        readonly public ?string $city = null,
        readonly public ?string $state = null,
    ){
    }
}