<?php

namespace App\Http\Controllers;

use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Log;
use Inertia\Inertia;
use Symfony\Component\HttpFoundation\StreamedResponse;
use App\Enums\EventType;
use App\Enums\DocumentType;
use App\Models\Document;
use App\Models\Event;
use App\Models\Requisition;
use App\Models\TakenDisciplines;
use App\Http\Requests\RequisitionCreationRequest;
use App\Models\RequisitionsPeriod;

class RequisitionController extends Controller
{   
    // todo: 
    // - manage permissions
    public function showRequisition($requisitionId) {        
        $user = Auth::user();
        $req = Requisition::with('takenDisciplines', 'documents')->find($requisitionId);

        // || (int) $req->student_nusp !== $user->codpes
        if (!$req ) {
            abort(404);
        }

        $documents = $req->documents->sortByDesc('created_at');

        $takenDiscsRecords = [];
        $currentCourseRecords = [];
        $takenDiscSyllabi = [];
        $requestedDiscSyllabi = [];

        foreach ($documents as $document) {
            switch ($document->type) {
                case DocumentType::TAKEN_DISCS_RECORD:
                    array_push($takenDiscsRecords, $document);
                    break;
                case DocumentType::CURRENT_COURSE_RECORD:
                    array_push($currentCourseRecords, $document);
                    break;
                case DocumentType::TAKEN_DISCS_SYLLABUS:
                    array_push($takenDiscSyllabi, $document);
                    break;
                case DocumentType::REQUESTED_DISC_SYLLABUS:
                    array_push($requestedDiscSyllabi, $document);
                    break;
            }
        }

        return Inertia::render('RequisitionDetail', ['req' => $req, 'takenDiscs' => $req->takenDisciplines, 'takenDiscsRecords' => $takenDiscsRecords, 'currentCourseRecords' => $currentCourseRecords, 'takenDiscSyllabi' => $takenDiscSyllabi, 'requestedDiscSyllabi' => $requestedDiscSyllabi]);
    }

    public function newRequisitionGet(){
        $userRoleId = Auth::user()->current_role_id;

        return Inertia::render('NewRequisition', ['isStudent' => $userRoleId == 1]);
    }

    public function newRequisitionPost(RequisitionCreationRequest $request)
    {
        $validatedRequest = $request->validated();
        try {
            DB::transaction(function () use ($validatedRequest) {
                $requisition = new Requisition;
                
                $user = Auth::user();
                if($user->current_role_id == 1){
                    $requisition->student_nusp = $user->codpes;
                    $requisition->student_name = $user->name;
                    $requisition->email = $user->email;
                }
                else {
                    $requisition->student_name = $validatedRequest['name'];
                    $requisition->email = $validatedRequest['email'];
                    $requisition->course = $validatedRequest['course'];
                }

                $requisition->requested_disc = $validatedRequest['requestedDiscName'];
                $requisition->requested_disc_type = $validatedRequest['requestedDiscType'];
                $requisition->requested_disc_code = $validatedRequest['requestedDiscCode'];
                $requisition->department = $validatedRequest['requestedDiscDepartment'];
                $requisition->situation = EventType::SENT_TO_SG;
                $requisition->internal_status = EventType::SENT_TO_SG;
                $requisition->result = 'Sem resultado';
                $requisition->result_text = null;
                $requisition->observations = $validatedRequest->observations ?? "";
                $requisition->validated = False;
                $requisition->latest_version = 1;
                $requisition->save();

                $takenDiscsRecord = new Document;
                $takenDiscsRecord->path = $validatedRequest['takenDiscRecord']->store('test');
                $takenDiscsRecord->requisition_id = $requisition->id;
                $takenDiscsRecord->type = DocumentType::TAKEN_DISCS_RECORD;
                $takenDiscsRecord->save();

                $currentCourseRecord = new Document;
                $currentCourseRecord->path = $validatedRequest['courseRecord']->store('test');
                $currentCourseRecord->requisition_id = $requisition->id;
                $currentCourseRecord->type = DocumentType::CURRENT_COURSE_RECORD;
                $currentCourseRecord->save();

                $takenDiscSyllabus = new Document;
                $takenDiscSyllabus->path = $validatedRequest['takenDiscSyllabus']->store('test');
                $takenDiscSyllabus->requisition_id = $requisition->id;
                $takenDiscSyllabus->type = DocumentType::TAKEN_DISCS_SYLLABUS;
                $takenDiscSyllabus->save();

                $requestedDiscSyllabus = new Document;
                $requestedDiscSyllabus->path = $validatedRequest['requestedDiscSyllabus']->store('test');
                $requestedDiscSyllabus->requisition_id = $requisition->id;
                $requestedDiscSyllabus->type = DocumentType::REQUESTED_DISC_SYLLABUS;
                $requestedDiscSyllabus->save();

                for ($i = 0; $i < $validatedRequest["takenDiscCount"]; $i++) {
                    $takenDisc = new TakenDisciplines;
                    $takenDisc->name = $validatedRequest["takenDiscNames"][$i];
                    $takenDisc->code = $validatedRequest["takenDiscCodes"][$i] ?? "";
                    $takenDisc->year = $validatedRequest["takenDiscYears"][$i];
                    $takenDisc->grade = number_format((float) $validatedRequest["takenDiscGrades"][$i], 2, '.', '');
                    $takenDisc->semester = $validatedRequest["takenDiscSemesters"][$i];
                    $takenDisc->institution = $validatedRequest["takenDiscInstitutions"][$i];
                    $takenDisc->requisition_id = $requisition->id;
                    $takenDisc->latest_version = 1;
                    $takenDisc->save();
                }

                $event = new Event;
                $event->type = EventType::SENT_TO_SG;
                $event->requisition_id = $requisition->id;
                $event->author_name = $user->name;
                $event->author_nusp = $user->codpes;
                $event->version = 1;
                $event->save();
            });

            return Inertia::location(route('list'));
        } catch (\Exception $e) {
            Log::error('Error on createRequisition: ' . $e->getMessage(), [
                'exception' => $e,
            ]);
            abort(500, $e->getMessage());
        }
    }

    // public function showFilters()
    // {
    //     $user = Auth::user();
    //     $roleId = $user->current_role_id;

    //     $courses = Requisition::select('course')->distinct()->get();
    //     $statuses = Requisition::select('internal_status')->distinct()->get();

    //     // Lista de departamentos
    //     $departments = ['Todos', 'MAC', 'MAE', 'MAT', 'MAP', 'Disciplina de fora do IME'];
    //     // Lista de tipos de disciplina
    //     $discTypes = ['Todos', 'Obrigatória', 'Optativa Eletiva', 'Optativa Livre', 'Extracurricular'];
    //     // Lista de situações corretas
    //     $internal_statusOptions = ['Todos', 'Deferido', 'Indeferido', 'Encaminhado para a Secretaria'];

    //     $options = compact('courses', 'statuses', 'departments', 'discTypes', 'internal_statusOptions');
    //     return Inertia::render('ExportRequisitions', ['roleId' => $roleId, 'userRoles' => $user->roles, 'options' => $options]);
    // }

    // public function filterAndExport(Request $request)
    // {
    //     $query = Requisition::with(['reviews', 'requisitionsVersions', 'events']);

    //     if ($request->department !== 'Todos') {
    //         $query->where('department', $request->department);
    //     }

    //     if ($request->internal_status !== 'Todos') {
    //         $query->where('result', $request->internal_status);
    //     }

    //     if ($request->requested_disc_type !== 'Todos') {
    //         $query->where('requested_disc_type', $request->requested_disc_type);
    //     }

    //     if ($request->start_date) {
    //         $query->where('created_at', '>=', $request->start_date);
    //     }

    //     if ($request->end_date) {
    //         $query->where('created_at', '<=', $request->end_date);
    //     }

    //     $requisitions = $query->get();

    //     $exportData = $requisitions->map(function ($requisition) {
    //         $sentToDepReqs = $requisition->getRelation('events')->filter(function ($item) {
    //             return $item->type == 'Enviado para análise do departamento';
    //         })->last();

    //         $registeredReqs = $requisition->getRelation('events')->filter(function ($item) {
    //             return $item->type == 'Aguardando avaliação da CG';
    //         })->last();

    //         $reviewIsEmpty = $requisition->getRelation('reviews')->isEmpty();

    //         $data = [
    //             'Nome' => $requisition->student_name,
    //             'Número USP' => $requisition->student_nusp,
    //             'Curso' => $requisition->course,
    //             'Data de abertura do Requerimento' => $requisition->created_at->format('d-m-Y'),
    //             'Disciplina a ser dispensada' => $requisition->requested_disc_code,
    //             'Departamento responsável' => $requisition->department,
    //             'Situação' => $requisition->internal_status,
    //             'Data de encaminhamento ao departamento/unidade' => $sentToDepReqs != null ? $sentToDepReqs->created_at->format('d-m-Y') : null,
    //             'Parecer' => $reviewIsEmpty ? null : $requisition->getRelation('reviews')[0]->reviewer_decision,
    //             'Parecerista' => $reviewIsEmpty ? null : $requisition->getRelation('reviews')[0]->reviewer_name,
    //             'Data do parecer' => $reviewIsEmpty ? null : $requisition->getRelation('reviews')[0]->updated_at->format('d-m-Y'),
    //             'Data do registro no Júpiter pelo Departamento' => $registeredReqs != null ? $registeredReqs->created_at->format('d-m-Y') : null
    //         ];

    //         return $data;
    //     });

    //     return new StreamedResponse(function () use ($exportData) {
    //         $handle = fopen('php://output', 'w');
    //         fputcsv($handle, array_keys($exportData->first()));

    //         foreach ($exportData as $row) {
    //             fputcsv($handle, $row);
    //         }

    //         fclose($handle);
    //     }, 200, [
    //         'Content-Type' => 'text/csv',
    //         'Content-Disposition' => 'attachment; filename="requisitions_export.csv"',
    //     ]);
    // }
}
