<?php

namespace App\Enums;

class DepartmentId
{
    const MAC = 1;
    const MAE = 2;
    const MAP = 3;
    const MAT = 4;
    const VRT = 5;

    public static function values()
    {
        return [
            self::MAC,
            self::MAE,
            self::MAP,
            self::MAT,
            self::VRT,
        ];
    }
}
