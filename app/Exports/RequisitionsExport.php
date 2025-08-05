<?php

namespace App\Exports;

use Maatwebsite\Excel\Concerns\FromCollection;
use Maatwebsite\Excel\Concerns\WithHeadings;

class RequisitionsExport implements FromCollection, WithHeadings
{
    private $data;

    function __construct($data) 
    {
        $this->data = $data;
    }

    public function collection()
    {
        return $this->data;
    }

    public function headings(): array
    {
        return [
            'Nome',
            'Número USP',
            'Curso',
            'Data de abertura do Requerimento',
            'Disciplina a ser dispensada',
            'Departamento responsável',
            'Situação',
            'Data de encaminhamento ao departamento/unidade',
            'Parecer',
            'Parecerista',
            'Data do parecer',
            'Data do registro no Júpiter pelo Departamento'
        ];
    }
}
