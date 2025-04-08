<?php

namespace App\Enums;

enum DocumentStatus: string
{
    case Pending = 'pending';
    case Approved = 'approved';
    case Rejected = 'rejected';

    public function label(): string
    {
        return match($this) {
            self::Pending => 'Pendente',
            self::Approved => 'Aprovado',
            self::Rejected => 'Rejeitado',
        };
    }
}