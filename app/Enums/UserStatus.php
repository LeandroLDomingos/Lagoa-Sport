<?php

namespace App\Enums;

enum UserStatus: string
{
    case Active = 'active';
    case Inactive = 'inactive';
    case Pending = 'pending';
    case IsAnalising = 'is_analising';
    case Blocked = 'blocked';

    public function label(): string
    {
        return match($this) {
            self::Active => 'Ativo',
            self::Inactive => 'Inativo',
            self::Pending => 'Pendente',
            self::IsAnalising => 'Em Análise',
            self::Blocked => 'Bloqueado',
        };
    }
}