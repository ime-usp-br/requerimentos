<?php

namespace App\Enums;

class EventType 
{
    const SENT_TO_SG = 'Encaminhado para a secretaria';
    const SENT_TO_REVIEWERS = 'Enviado para análise dos pareceristas';
    const BACK_TO_STUDENT = 'Retornado para o aluno devido a inconsistência nas informações';
    const ACCEPTED = 'Deferido';
    const REJECTED = 'Indeferido';
    const RETURNED_BY_REVIEWER = 'Retornado por um parecerista';
    const IN_REVALUATION = 'Em reavaliação';
    const RESEND_BY_STUDENT = 'Reenviado pelo aluno depois de atualização';
}
