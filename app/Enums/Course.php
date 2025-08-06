<?php

namespace App\Enums;

class Course 
{
    const BCC = 'Bacharelado em Ciência da Computação';
    const STATISTICS = 'Bacharelado em Estatística';
    const MAT_LIC = 'Licenciatura em Matemática';
    const MAT_PURE = 'Bacharelado em Matemática';
    const MAT_COMP_APPLIED = 'Bacharelado em Matemática Aplicada e Computacional';
    const MAT_APPLIED = 'Bacharelado em Matemática Aplicada';

    public static function values()
    {
        return [
            self::BCC,
            self::STATISTICS,
            self::MAT_LIC,
            self::MAT_PURE,
            self::MAT_COMP_APPLIED,
            self::MAT_APPLIED,
        ];
    }
}
