<?php

namespace App\Enums;

class EventType 
{
    const SENT_TO_SG = 'Encaminhado para a SG pelo aluno';
    const SENT_TO_REVIEWERS = 'Enviado para análise dos pareceristas';
    const BACK_TO_STUDENT = 'Retornado para o aluno devido a inconsistência nos dados';
    const ACCEPTED = 'Requerimento deferido';
    const REJECTED = 'Requerimento indeferido';
    const RETURNED_BY_REVIEWER = 'Retornado por um parecerista';
    const IN_REVALUATION = 'Requerimento em reavaliação';
    const RESEND_BY_STUDENT = 'Reenviado pelo aluno depois de atualização';
    const SENT_TO_DEPARTMENT = 'Enviado para análise do departamento';
    const REGISTERED = 'Aguardando avaliação da CG';
}
