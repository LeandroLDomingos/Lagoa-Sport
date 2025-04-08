<?php

namespace App\Enums;

enum DocumentType: string
{
    case IdentityFront = 'identity-front';
    case IdentityBack = 'identity-back';
    case ResidenceProof = 'residence_proof';

    public function label(): string
    {
        return match($this) {
            self::IdentityFront => 'Frente do Documento de Identidade',
            self::IdentityBack => 'Verso do Documento de Identidade',
            self::ResidenceProof => 'Comprovante de Residência',
        };
    }
}