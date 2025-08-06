<?php

namespace App\Enums;

class DisciplineType 
{
    const MANDATORY = 'Obrigatória';
    const EXTRACURRICULAR = 'Extracurricular';
    const OPTIONAL_FREE = 'Optativa Livre';
    const OPTIONAL_ELECTIVE = 'Optativa Eletiva';

    public static function values()
    {
        return [
            self::MANDATORY,
            self::EXTRACURRICULAR,
            self::OPTIONAL_FREE,
            self::OPTIONAL_ELECTIVE,
        ];
    }
}
