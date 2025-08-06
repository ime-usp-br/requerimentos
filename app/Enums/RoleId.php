<?php

namespace App\Enums;

class RoleId
{
    const STUDENT = 1;
    const SG = 2;
    const REVIEWER = 3;
    const SECRETARY = 4;

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