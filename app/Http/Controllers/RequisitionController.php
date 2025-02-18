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
        $departments = ['Todos', 'MAC', 'MAE', 'MAT', 'MAP'];

        // Lista de tipos de disciplina
        $discTypes = ['Todos', 'Obrigatória', 'Optativa Eletiva', 'Optativa Livre', 'Extracurricular'];

        // Lista de situações corretas
        $internal_statusOptions = ['Todos', 'Deferido', 'Indeferido', 'Encaminhado para a Secretaria'];

        return view('pages.requisitions.filters', compact('courses', 'statuses', 'departments', 'discTypes', 'internal_statusOptions'));
    }

    public function filterAndExport(Request $request)
    {
        $query = Requisition::with(['reviews', 'requisitionsVersions', 'events']);

        if ($request->department !== 'Todos') {
            $query->where('department', $request->department);
        }

        if ($request->internal_status !== 'Todos') {
            $query->where('result', $request->internal_status);
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

        $exportData = $requisitions->map(function($requisition) {
            $sentToDepReqs = $requisition->getRelation('events')->filter(function($item) {
                return $item->type == 'Enviado para análise do departamento';
            })->last();

            $registeredReqs = $requisition->getRelation('events')->filter(function($item) {
                return $item->type == 'Aguardando avaliação da CG';
            })->last();

            $reviewIsEmpty = $requisition->getRelation('reviews')->isEmpty();

            $data = [
                'Nome' => $requisition->student_name,
                'Número USP' => $requisition->student_nusp,
                'Curso' => $requisition->course,
                'Data de abertura do Requerimento' => $requisition->created_at->format('d-m-Y'),
                'Disciplina a ser dispensada' => $requisition->requested_disc_code,
                'Departamento responsável' => $requisition->department,
                'Situação' => $requisition->internal_status,
                'Data de encaminhamento ao departamento/unidade' => $sentToDepReqs != null ? $sentToDepReqs->created_at->format('d-m-Y') : null,
                'Parecer' => $reviewIsEmpty ? null : $requisition->getRelation('reviews')[0]->reviewer_decision,
                'Parecerista' => $reviewIsEmpty ? null : $requisition->getRelation('reviews')[0]->reviewer_name,
                'Justificativa do Parecer' => $reviewIsEmpty ? null : $requisition->getRelation('reviews')[0]->justification,
                'Data do parecer' => $reviewIsEmpty ? null : $requisition->getRelation('reviews')[0]->updated_at->format('d-m-Y'),
                'Data do registro no Júpiter pelo Departamento' => $registeredReqs != null ? $registeredReqs->created_at->format('d-m-Y') : null
            ];

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
