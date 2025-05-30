<?php

namespace App\Enums;

class ResultType 
{
    const PENDING = 'Sem resultado';
    const ACCEPTED = 'Deferido';
    const REJECTED = 'Indeferido';
    const INCONSISTENT = 'Inconsistência nas informações';
    const CANCELLED = 'Cancelado';
}
