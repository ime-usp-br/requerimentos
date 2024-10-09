<?php

namespace App\Http\Controllers;

use App\Models\Requisition;
use Symfony\Component\HttpFoundation\StreamedResponse;

class RequisitionController extends Controller
{
    public function exportCSV()
    {
        $requisitions = Requisition::all();

        $response = new StreamedResponse(function() use ($requisitions) {
            $handle = fopen('php://output', 'w');

            fputcsv($handle, ['Aluno', 'Número USP', 'Códido da disciplina', 'Tipo da disciplina', 'Situação']);

            foreach ($requisitions as $requisition) {
                fputcsv($handle, [                    
                    $requisition->student_name,
                    $requisition->student_nusp,
                    $requisition->requested_disc_code,
                    $requisition->requested_disc_type,
                    $requisition->internal_status,
                    
                ]);
            }

            fclose($handle);
        });

        $response->headers->set('Content-Type', 'text/csv');
        $response->headers->set('Content-Disposition', 'attachment; filename="requisitions.csv"');

        return $response;
    }
}
