<?php

namespace App\Enums;

class DepartmentName 
{
    const MAC = 'MAC';
    const MAE = 'MAE';
    const MAP = 'MAP';
    const MAT = 'MAT';
    const EXTERNAL = 'Disciplina de fora do IME';

    public static function values()
    {
        return [
            self::MAC,
            self::MAE,
            self::MAP,
            self::MAT,
            self::EXTERNAL,
        ];
    }
}
