<?php

namespace App\Enums;

class EventType
{
    const SENT_TO_SG = 'Encaminhado para a SG pelo aluno';
    const BACK_TO_STUDENT = 'Retornado para o aluno devido a inconsistência nos dados';
    const UPDATED_BY_STUDENT = 'Informações atualizadas pelo aluo';
    const UPDATED_BY_SG = 'Informações atualizadas pela SG';
    const RESENT_BY_STUDENT = 'Reenviado pelo aluno depois de atualização';
    const SENT_TO_DEPARTMENT = 'Enviado para análise do departamento';
    const SENT_TO_REVIEWERS = 'Enviado para análise dos pareceristas';
    const RETURNED_BY_REVIEWER = 'Retornado por um parecerista';
    const AUTOMATIC_DEFERRAL = 'Parecer deferido automaticamente';
    const REGISTERED = 'Aguardando avaliação da CG';
    const ACCEPTED = 'Requerimento deferido';
    const REJECTED = 'Requerimento indeferido';
    const IN_REVALUATION = 'Requerimento em reavaliação';
    const CANCELLED = 'Requerimento cancelado';
    const BACK_TO_SG = 'Retornado para a SG devido a inconsistência nos dados';

    public static function values()
    {
        return [
            self::SENT_TO_SG,
            self::BACK_TO_STUDENT,
            self::UPDATED_BY_STUDENT,
            self::UPDATED_BY_SG,
            self::RESENT_BY_STUDENT,
            self::SENT_TO_DEPARTMENT,
            self::SENT_TO_REVIEWERS,
            self::RETURNED_BY_REVIEWER,
            self::AUTOMATIC_DEFERRAL,
            self::REGISTERED,
            self::ACCEPTED,
            self::REJECTED,
            self::IN_REVALUATION,
            self::CANCELLED,
            self::BACK_TO_SG,
        ];
    }
}
