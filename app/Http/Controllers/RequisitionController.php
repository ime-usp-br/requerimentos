<?php

namespace App\Http\Controllers;

use App\Models\Requisition;
use Illuminate\Http\Request;
use Symfony\Component\HttpFoundation\StreamedResponse;

class RequisitionController extends Controller
{
    public function showFilters()
    {
        $courses = Requisition::select('course')->distinct()->get();
        $statuses = Requisition::select('internal_status')->distinct()->get();
        
        // Lista de departamentos
        $departments = ['MAC', 'MAE', 'MAT', 'MAP', 'Todos'];

        // Lista de tipos de disciplina
        $discTypes = ['Obrigatória', 'Optativa Eletiva', 'Optativa Livre', 'Extracurricular', 'Todos'];

        // Lista de situações corretas
        $internal_statusOptions = ['Deferido', 'Indeferido', 'Encaminhado para a Secretaria', 'Todos'];

        return view('pages.requisitions.filters', compact('courses', 'statuses', 'departments', 'discTypes', 'internal_statusOptions'));
    }

    public function filterAndExport(Request $request)
    {
        $query = Requisition::with(['reviews', 'takenDisciplines']);

        if ($request->department !== 'Todos') {
            $query->where('department', $request->department);
        }

        if ($request->internal_status !== 'Todos') {
            $query->where('internal_status', $request->internal_status);
        }

        if ($request->requested_disc_type !== 'Todos') {
            $query->where('requested_disc_type', $request->requested_disc_type);
        }

        if ($request->start_date) {
            $query->where('created_at', '>=', $request->start_date);
        }

        if ($request->end_date) {
            $query->where('created_at', '<=', $request->end_date);
        }

        $requisitions = $query->get();

        $exportData = $requisitions->map(function($requisition) use ($request) {
            $data = [
                'Nome' => $requisition->student_name,
                'NUSP' => $requisition->nusp,
                'Curso' => $requisition->course,
                'Codigo Disciplina' => $requisition->requested_disc_code,
                'Tipo Disciplina' => $requisition->requested_disc_type,
                'Departamento' => $requisition->department,
                'Data' => $requisition->created_at->format('Y-m-d'),
            ];

            if ($request->export_type !== 'sg_meeting') {
                $data['Faculdade'] = optional($requisition->takenDisciplines->first())->institution ?: 'N/A';
                $data['Data Parecer'] = optional(optional($requisition->reviews->first())->updated_at)->format('Y-m-d') ?: 'N/A';
                $data['Resultado Parecer'] = optional($requisition->reviews->first())->reviewer_decision ?: 'N/A';
                $data['Texto Parecer'] = optional($requisition->reviews->first())->justification ?: 'N/A';
                $data['NUSP Parecerista'] = optional($requisition->reviews->first())->reviewer_nusp ?: 'N/A';
                $data['Data Resultado Final'] = optional($requisition->updated_at)->format('Y-m-d') ?: 'N/A';
                $data['Resultado Final'] = $requisition->result ?: 'N/A';
            }

            return $data;
        });

        return new StreamedResponse(function() use ($exportData) {
            $handle = fopen('php://output', 'w');
            fputcsv($handle, array_keys($exportData->first()));

            foreach ($exportData as $row) {
                fputcsv($handle, $row);
            }

            fclose($handle);
        }, 200, [
            'Content-Type' => 'text/csv',
            'Content-Disposition' => 'attachment; filename="requisitions_export.csv"',
        ]);
    }
}
