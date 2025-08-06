<?php

namespace App\Enums;

class DocumentType 
{
    const TAKEN_DISCS_RECORD = 'Histórico com as disciplinas cursadas';
    const CURRENT_COURSE_RECORD = 'Histórico do curso atual';
    const TAKEN_DISCS_SYLLABUS = 'Ementas das disciplinas cursadas';
    const REQUESTED_DISC_SYLLABUS = 'Ementa da disciplina requerida';

    public static function values()
    {
        return [
            self::TAKEN_DISCS_RECORD,
            self::CURRENT_COURSE_RECORD,
            self::TAKEN_DISCS_SYLLABUS,
            self::REQUESTED_DISC_SYLLABUS,
        ];
    }
}
