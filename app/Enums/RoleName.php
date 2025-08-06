<?php

namespace App\Enums;

class RoleName 
{
    const STUDENT = 'Aluno';
    const SG = 'Serviço de Graduação';
    const REVIEWER = 'Parecerista';
    const SECRETARY = 'Secretaria';

    public static function values()
    {
        return [
            self::STUDENT,
            self::SG,
            self::REVIEWER,
            self::SECRETARY,
        ];
    }
}
