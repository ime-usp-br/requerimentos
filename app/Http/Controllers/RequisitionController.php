<?php

namespace App\Http\Controllers;

use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Log;
use Inertia\Inertia;
use App\Enums\RoleId;
use App\Enums\EventType;
use App\Enums\DocumentType;
use App\Models\Document;
use App\Models\Event;
use App\Models\Requisition;
use App\Models\Review;
use App\Models\TakenDisciplines;
use App\Models\TakenDisciplinesVersion;
use App\Models\RequisitionsVersion;
use App\Http\Requests\RequisitionCreationRequest;
use App\Http\Requests\RequisitionUpdateRequest;
use Symfony\Component\HttpFoundation\StreamedResponse;

class RequisitionController extends Controller
{
    public function showRequisition($requisitionId)
    {
        $user = Auth::user();
        $requisition = Requisition::with('takenDisciplines', 'documents')->find($requisitionId);

        if (!$requisition) {
            abort(404);
        }
        if ($user->current_role_id == RoleId::STUDENT && (int) $requisition->student_nusp !== (int) $user->codpes) {
            abort(403);
        }

        $documents = $requisition->documents->sortByDesc('created_at');

        // Only keep the latest version of each document type
        $latestDocuments = [];
        foreach ($documents as $document) {
            $type = $document->type;
            if (!isset($latestDocuments[$type]) || $document->version > $latestDocuments[$type]->version) {
                $latestDocuments[$type] = $document;
            }
        }
        $latestDocumentsArray = array_values($latestDocuments);

        $roleId = $user->current_role_id;
        switch ($roleId) {
            case RoleId::STUDENT:
                $selectedActions = [];
                break;
            case RoleId::SG:
                $selectedActions = [['send_to_department',
                                     'result'],
                                    ['edit_requisition'], 
                                    ['send_to_reviewers', 
                                     'reviews'], 
                                    ['requisition_history'], 
                                    ['registered',
                                     'automatic_requisition',
                                     'export_current']];
                break;
            case RoleId::SECRETARY:
                $selectedActions =  [['send_to_reviewers', 
                                       'reviews'], 
                                     ['requisition_history'],
                                     ['registered']];
                break;
            case RoleId::REVIEWER:
                $selectedActions = [['submit_review']];
                break;
        }


        return Inertia::render('RequisitionDetailPage', [
            'label' => 'Requerimentos', 
            'roleId' => $roleId,
            'userRoles' => $user->roles,
            'selectedActions' => $selectedActions,
            'requisition' => $requisition,
            'takenDiscs' => $requisition->takenDisciplines,
            'documents' => $latestDocumentsArray,
        ]);
    }

    public function newRequisitionGet()
    {
        $user = Auth::user();
        return Inertia::render('RequisitionFormPage', ['isStudent' => $user->current_role_id == RoleId::STUDENT,
                                                    'label' => 'Novo Requerimento',
                                                    'roleId' => $user->current_role_id,
                                                    'userRoles' => $user->roles, 
                                                    'isUpdate' => false,
                                                    ]);
    }

    public function newRequisitionPost(RequisitionCreationRequest $request)
    {
        $validatedRequest = $request->validated();

        try {
            DB::transaction(function () use ($validatedRequest) {
                $requisition = new Requisition;

                $AuthUser = Auth::user();
                if ($AuthUser->current_role_id == 1) {
                    $requisition->student_nusp = $AuthUser->codpes;
                    $requisition->student_name = $AuthUser->name;
                    $requisition->email = $AuthUser->email;
                } else {
                    $requisition->student_nusp = $validatedRequest['student_nusp'];
                    $requisition->student_name = $validatedRequest['student_name'];
                    $requisition->email = $validatedRequest['email'];
                }

                $requisition->course = $validatedRequest['course'];
                $requisition->requested_disc = $validatedRequest['requestedDiscName'];
                $requisition->requested_disc_type = $validatedRequest['requestedDiscType'];
                $requisition->requested_disc_code = $validatedRequest['requestedDiscCode'];
                $requisition->department = $validatedRequest['requestedDiscDepartment'];
                $requisition->situation = EventType::SENT_TO_SG;
                $requisition->internal_status = EventType::SENT_TO_SG;
                $requisition->result = 'Sem resultado';
                $requisition->result_text = null;
                $requisition->observations = $validatedRequest["observations"] ?? "";
                $requisition->latest_version = 1;
                $requisition->editable = false;
                $requisition->save();

                $takenDiscsRecord = new Document;
                $takenDiscsRecord->path = $validatedRequest['takenDiscRecord']->store('test');
                $takenDiscsRecord->requisition_id = $requisition->id;
                $takenDiscsRecord->type = DocumentType::TAKEN_DISCS_RECORD;
                $takenDiscsRecord->version = 1;
                $takenDiscsRecord->hash = hash_file('sha256', $validatedRequest['takenDiscRecord']->getRealPath());
                $takenDiscsRecord->save();

                $currentCourseRecord = new Document;
                $currentCourseRecord->path = $validatedRequest['courseRecord']->store('test');
                $currentCourseRecord->requisition_id = $requisition->id;
                $currentCourseRecord->type = DocumentType::CURRENT_COURSE_RECORD;
                $currentCourseRecord->version = 1;
                $currentCourseRecord->hash = hash_file('sha256', $validatedRequest['courseRecord']->getRealPath());
                $currentCourseRecord->save();

                $takenDiscSyllabus = new Document;
                $takenDiscSyllabus->path = $validatedRequest['takenDiscSyllabus']->store('test');
                $takenDiscSyllabus->requisition_id = $requisition->id;
                $takenDiscSyllabus->type = DocumentType::TAKEN_DISCS_SYLLABUS;
                $takenDiscSyllabus->version = 1;
                $takenDiscSyllabus->hash = hash_file('sha256', $validatedRequest['takenDiscSyllabus']->getRealPath());
                $takenDiscSyllabus->save();

                $requestedDiscSyllabus = new Document;
                $requestedDiscSyllabus->path = $validatedRequest['requestedDiscSyllabus']->store('test');
                $requestedDiscSyllabus->requisition_id = $requisition->id;
                $requestedDiscSyllabus->type = DocumentType::REQUESTED_DISC_SYLLABUS;
                $requestedDiscSyllabus->version = 1;
                $requestedDiscSyllabus->hash = hash_file('sha256', $validatedRequest['requestedDiscSyllabus']->getRealPath());
                $requestedDiscSyllabus->save();

                for ($i = 0; $i < $validatedRequest["takenDiscCount"]; $i++) {
                    $takenDisc = new TakenDisciplines;
                    $takenDisc->name = $validatedRequest["takenDiscNames"][$i];
                    $takenDisc->code = $validatedRequest["takenDiscCodes"][$i] ?? "";
                    $takenDisc->year = $validatedRequest["takenDiscYears"][$i];
                    $takenDisc->grade = $validatedRequest["takenDiscGrades"][$i];
                    $takenDisc->semester = $validatedRequest["takenDiscSemesters"][$i];
                    $takenDisc->institution = $validatedRequest["takenDiscInstitutions"][$i];
                    $takenDisc->requisition_id = $requisition->id;
                    $takenDisc->latest_version = 1;
                    $takenDisc->save();
                }

                $event = new Event;
                $event->type = EventType::SENT_TO_SG;
                $event->requisition_id = $requisition->id;
                $event->author_name = $AuthUser->name;
                $event->author_nusp = $AuthUser->codpes;
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

    public function updateRequisitionGet($requisitionId)
    {
        $this->checkUserUpdatePermission($requisitionId);
        $requisition = Requisition::with('takenDisciplines', 'documents')->find($requisitionId);

        $requisitionData = [
            'requisitionId' => $requisition->id,
            'student_name' => $requisition->student_name,
            'email' => $requisition->email,
            'student_nusp' => $requisition->student_nusp,
            'course' => $requisition->course,
            'requestedDiscName' => $requisition->requested_disc,
            'requestedDiscType' => $requisition->requested_disc_type,
            'requestedDiscCode' => $requisition->requested_disc_code,
            'requestedDiscDepartment' => $requisition->department,
            'takenDiscNames' => $requisition->takenDisciplines->pluck('name')->toArray(),
            'takenDiscInstitutions' => $requisition->takenDisciplines->pluck('institution')->toArray(),
            'takenDiscCodes' => $requisition->takenDisciplines->pluck('code')->toArray(),
            'takenDiscYears' => $requisition->takenDisciplines->pluck('year')->toArray(),
            'takenDiscGrades' => $requisition->takenDisciplines->pluck('grade')->toArray(),
            'takenDiscSemesters' => $requisition->takenDisciplines->pluck('semester')->toArray(),
            'takenDiscCount' => $requisition->takenDisciplines->count(),
            'takenDiscRecord' => $requisition->documents->where('type', DocumentType::TAKEN_DISCS_RECORD)->first()->path ?? "",
            'courseRecord' => $requisition->documents->where('type', DocumentType::CURRENT_COURSE_RECORD)->first()->path ?? "",
            'takenDiscSyllabus' => $requisition->documents->where('type', DocumentType::TAKEN_DISCS_SYLLABUS)->first()->path ?? "",
            'requestedDiscSyllabus' => $requisition->documents->where('type', DocumentType::REQUESTED_DISC_SYLLABUS)->first()->path ?? "",
            'observations' => $requisition->observations,
        ];

        $user = Auth::user();

        return Inertia::render('RequisitionFormPage', [
            'requisitionData' => $requisitionData,
            'label' => 'Novo Requerimento',
            'roleId' => $user->current_role_id,
            'userRoles' => $user->roles, 
            'isStudent' => $user->current_role_id == RoleId::STUDENT,
            'isUpdate' => true,
        ]);
    }

    public function updateRequisitionPost(RequisitionUpdateRequest $request)
    {
        $validatedRequest = $request->validated();
        $this->checkUserUpdatePermission($validatedRequest["requisitionId"]); 
        $requisition = Requisition::find($validatedRequest["requisitionId"]);

        $changedDocuments = $this->changedDocuments($requisition, $validatedRequest);
        $hasTakenDisciplinesChanged = $this->hasTakenDisciplinesChanged($validatedRequest);
        $hasRequisitionDataChanged = $this->hasRequisitionDataChanged($validatedRequest);
        $hasChanges = !empty($changedDocuments) || $hasTakenDisciplinesChanged || $hasRequisitionDataChanged;   
        if ($hasChanges) {
            try {
                DB::transaction(function () use ($changedDocuments, $hasTakenDisciplinesChanged, $requisition, $validatedRequest) {
                    $versions = [];

                    foreach ($changedDocuments as $document) {
                        $versions[$document['type']] = $this->updateDocumentData($requisition->id, $document['document'], $document['type']);
                    }

                    if ($hasTakenDisciplinesChanged) {
                        $versions["taken_disciplines_version"] = $this->updateTakenDisciplinesData($validatedRequest);
                    }

                    $this->updateRequisitionData($requisition, $validatedRequest, $versions);

                    $requisition->refresh();
                    $event = new Event;
                    $event->type = EventType::UPDATED_BY_STUDENT;
                    $event->requisition_id = $validatedRequest["requisitionId"];
                    $event->author_name = Auth::user()->name;
                    $event->author_nusp = Auth::user()->codpes;
                    $event->version = $requisition->latest_version;
                    $event->save();
                });
            } catch (\Exception $e) {
                Log::error('Error on updateRequisition: ' . $e->getMessage(), [
                    'exception' => $e,
                ]);
                abort(500, $e->getMessage());
            }
            return Inertia::location(route('list'));
        } 
        else {
            abort(400, 'No changes were submitted.');
        }
    }

    private function checkUserUpdatePermission($requisitionId){
        $requisition = Requisition::find($requisitionId);

        $user = Auth::user();

        if (!$requisition) {
            abort(404);
        }
        if ((!$requisition->editable && $user->current_role_id != RoleID::SG) ||
            ($user->current_role_id == RoleId::STUDENT && $requisition->student_nusp != $user->codpes)
        ) {
            abort(403);
        }
    }

    private function changedDocuments($requisition, $updateRequest)
    {
        $existingDocuments = Document::where('requisition_id', $requisition->id)
            ->where('version', $requisition->latest_version)
            ->get();

        $newDocuments = [
            DocumentType::TAKEN_DISCS_RECORD => $updateRequest['takenDiscRecord'],
            DocumentType::CURRENT_COURSE_RECORD => $updateRequest['courseRecord'],
            DocumentType::TAKEN_DISCS_SYLLABUS => $updateRequest['takenDiscSyllabus'],
            DocumentType::REQUESTED_DISC_SYLLABUS => $updateRequest['requestedDiscSyllabus'],
        ];

        $changedDocuments = [];

        foreach ($existingDocuments as $existingDocument) {
            $documentType = $existingDocument->type;

            $newDocumentHash = hash_file('sha256', $newDocuments[$documentType]);
            if ($existingDocument->hash !== $newDocumentHash) {
                $changedDocuments[] = [
                    'type' => $documentType,
                    'document' => $newDocuments[$documentType]
                ];
            }
        }

        return $changedDocuments;
    }

    private function hasTakenDisciplinesChanged($updateRequest)
    {
        $hasChanged = False;

        $existingTakenDisciplines = TakenDisciplines::where('requisition_id', $updateRequest["requisitionId"])->get();

        $existingTakenDisciplinesArray = $existingTakenDisciplines->map(function ($discipline) {
            return [
                'name' => $discipline->name,
                'code' => $discipline->code,
                'year' => $discipline->year,
                'grade' => $discipline->grade,
                'semester' => $discipline->semester,
                'institution' => $discipline->institution,
            ];
        })->toArray();

        $newTakenDisciplinesArray = [];
        for ($i = 0; $i < $updateRequest["takenDiscCount"]; $i++) {
            $newTakenDisciplinesArray[] = [
                'name' => $updateRequest["takenDiscNames"][$i],
                'code' => $updateRequest["takenDiscCodes"][$i] ?? "",
                'year' => $updateRequest["takenDiscYears"][$i],
                'grade' => $updateRequest["takenDiscGrades"][$i],
                'semester' => $updateRequest["takenDiscSemesters"][$i],
                'institution' => $updateRequest["takenDiscInstitutions"][$i],
            ];
        }

        // Debugging information

        if ($existingTakenDisciplinesArray != $newTakenDisciplinesArray) {
            $hasChanged = True;
        }


        return $hasChanged;
    }

    private function hasRequisitionDataChanged($updateRequest)
    {
        $requisition = Requisition::find($updateRequest["requisitionId"]);
        $hasChanged = False;
        if (
            $requisition->department !== $updateRequest["requestedDiscDepartment"]
            || $requisition->observations !== $updateRequest["observations"]
        ) {
            $hasChanged = True;
        }

        return $hasChanged;
    }

    private function updateDocumentData($requisitionId, $documentData, $documentType)
    {
        $lastDocument = Document::where('requisition_id', $requisitionId)
            ->where('type', $documentType)
            ->orderBy('version', 'desc')
            ->first();

        $document = new Document;
        $document->path = $documentData->store('test');
        $document->hash = hash_file('sha256', $documentData);
        $document->version = $lastDocument->version + 1;
        $document->requisition_id = $requisitionId;
        $document->type = $documentType;
        $document->save();

        return $lastDocument->version;
    }

    private function updateTakenDisciplinesData($updateRequest)
    {
        $existingTakenDisciplines = TakenDisciplines::where('requisition_id', $updateRequest["requisitionId"])->get();

        foreach ($existingTakenDisciplines as $existingTakenDiscipline) {
            $takenDiscVersion = new TakenDisciplinesVersion;
            $takenDiscVersion->name = $existingTakenDiscipline->name;
            $takenDiscVersion->code = $existingTakenDiscipline->code;
            $takenDiscVersion->year = $existingTakenDiscipline->year;
            $takenDiscVersion->grade = $existingTakenDiscipline->grade;
            $takenDiscVersion->semester = $existingTakenDiscipline->semester;
            $takenDiscVersion->institution = $existingTakenDiscipline->institution;
            $takenDiscVersion->requisition_id = $existingTakenDiscipline->requisition_id;
            $takenDiscVersion->version = $existingTakenDiscipline->latest_version;
            $takenDiscVersion->save();
        }

        TakenDisciplines::where('requisition_id', $updateRequest["requisitionId"])->delete();

        for ($i = 0; $i < $updateRequest["takenDiscCount"]; $i++) {
            $takenDisc = new TakenDisciplines;
            $takenDisc->name = $updateRequest["takenDiscNames"][$i];
            $takenDisc->code = $updateRequest["takenDiscCodes"][$i] ?? "";
            $takenDisc->year = $updateRequest["takenDiscYears"][$i];
            $takenDisc->grade = $updateRequest["takenDiscGrades"][$i];
            $takenDisc->semester = $updateRequest["takenDiscSemesters"][$i];
            $takenDisc->institution = $updateRequest["takenDiscInstitutions"][$i];
            $takenDisc->requisition_id = $updateRequest["requisitionId"];
            $takenDisc->latest_version = $existingTakenDisciplines->isNotEmpty() ? $existingTakenDisciplines->max('latest_version') + 1 : 1;
            $takenDisc->save();
        }


        return $takenDiscVersion->version;
    }

    private function updateRequisitionData($requisition, $updateRequest, $versions)
    {
        $requisitionVersion = new RequisitionsVersion;
        $requisitionVersion->requisition_id = $requisition->id;
        $requisitionVersion->department = $requisition->department;
        $requisitionVersion->student_nusp = $requisition->student_nusp;
        $requisitionVersion->student_name = $requisition->student_name;
        $requisitionVersion->email = $requisition->email;
        $requisitionVersion->course = $requisition->course;
        $requisitionVersion->requested_disc = $requisition->requested_disc;
        $requisitionVersion->requested_disc_type = $requisition->requested_disc_type;
        $requisitionVersion->requested_disc_code = $requisition->requested_disc_code;
        $requisitionVersion->observations = $requisition->observations;
        $requisitionVersion->result = $requisition->result;
        $requisitionVersion->result_text = $requisition->result_text;
        $requisitionVersion->version = $requisition->latest_version;

        if ($requisition->latest_version > 1) {
            $latestRequisitionVersion = RequisitionsVersion::where('requisition_id', $requisition->id)
                ->where('version', $requisition->latest_version - 1)
                ->first();
        }
        $initialVersion = 1;
        $requisitionVersion->taken_disciplines_version = $versions['taken_disciplines_version']
            ?? ($latestRequisitionVersion->taken_disciplines_version
                ?? $initialVersion);
        $requisitionVersion->taken_disc_record_version = $versions[DocumentType::TAKEN_DISCS_RECORD]
            ?? ($latestRequisitionVersion->taken_disc_record_version
                ?? $initialVersion);
        $requisitionVersion->course_record_version = $versions[DocumentType::CURRENT_COURSE_RECORD]
            ?? ($latestRequisitionVersion->course_record_version
                ?? $initialVersion);
        $requisitionVersion->taken_disc_syllabus_version = $versions[DocumentType::TAKEN_DISCS_SYLLABUS]
            ?? ($latestRequisitionVersion->taken_disc_syllabus_version
                ?? $initialVersion);
        $requisitionVersion->requested_disc_syllabus_version = $versions[DocumentType::REQUESTED_DISC_SYLLABUS]
            ?? ($latestRequisitionVersion->requested_disc_syllabus_version
                ?? $initialVersion);

        $requisitionVersion->save();

        $requisition->department = $updateRequest['requestedDiscDepartment'];
        $requisition->observations = $updateRequest["observations"];

        if (Auth::user()->current_role_id == RoleId::STUDENT) {
            $requisition->situation = EventType::RESENT_BY_STUDENT;
            $requisition->internal_status = EventType::RESENT_BY_STUDENT;
        }
        $requisition->latest_version = $requisition->latest_version + 1;
        $requisition->save();
    }

    public function sendToDepartment(Request $request) {
        $requisition = Requisition::find($request['requisitionId']);

        $event = new Event;
        $event->type = EventType::SENT_TO_DEPARTMENT;
        $event->requisition_id = $request['requisitionId'];
        $event->author_name = Auth::user()->name; 
        $event->author_nusp = Auth::user()->codpes;
        $event->version = $requisition->latest_version;  
        $event->save();

        $requisition->situation = EventType::SENT_TO_DEPARTMENT;
        $requisition->internal_status = EventType::SENT_TO_DEPARTMENT;
        $requisition->registered = false;
        $requisition->save();

        return response('', 200)->header('Content-Type', 'text/plain');
    }

    public function automaticDeferral(Request $request) {
        DB::transaction(function () use ($request) {
            $user = Auth::user();
            $requisition = Requisition::find($request['requisitionId']);
            $requisitionId = $request['requisitionId'];
            $requisition->situation = EventType::RETURNED_BY_REVIEWER;
            $requisition->internal_status = EventType::RETURNED_BY_REVIEWER;
            $requisition->registered = false;
            $requisition->save();

            // Cria um parecer "deferido" e salva
            $review = new Review;
            $review->reviewer_name = $user->name;
            $review->reviewer_nusp = $user->codpes;
            $review->requisition_id = $requisitionId;
            $review->reviewer_decision = EventType::ACCEPTED;
            $review->justification = 'Deferimento automático';
            $review->latest_version = 0;
            $review->save();

            // Cria um novo evento para registrar o parecer
            $event = new Event;
            $event->type = EventType::RETURNED_BY_REVIEWER;
            $event->requisition_id = $requisitionId;
            $event->author_name = $user->name;
            $event->author_nusp = $user->codpes;
            $event->version = $requisition->latest_version;
            $event->save();
        });        

        return response('', 200)->header('Content-Type', 'text/plain');
    } 

    public function registered(Request $request) {
        DB::transaction(function () use ($request) {
            $user = Auth::user();

            $requisition = Requisition::find($request->requisitionId);
            $requisition->situation = EventType::REGISTERED;
            $requisition->registered = true;
            $requisition->internal_status = "Registrado no Jupiter por " . $user->name;
            $requisition->save();

            $event = new Event;
            $event->type = EventType::REGISTERED;
            $event->requisition_id = $request->requisitionId;
            $event->author_name = $user->name;
            $event->author_nusp = $user->codpes;
            $event->version = $requisition->latest_version;
            $event->message = "Registrado no Jupiter por " . $user->name;
            $event->save();
        });

        return response('', 200)->header('Content-Type', 'text/plain');
    }

    public function exportRequisitionsGet()
    {
        $user = Auth::user();
        $roleId = $user->current_role_id;

        $courses = Requisition::select('course')->distinct()->get();
        $statuses = Requisition::select('internal_status')->distinct()->get();

        $departments = ['Todos', 'MAC', 'MAE', 'MAT', 'MAP', 'Disciplina de fora do IME'];
        $discTypes = ['Todos', 'Obrigatória', 'Optativa Eletiva', 'Optativa Livre', 'Extracurricular'];
        $internal_statusOptions = ['Todos', 'Deferido', 'Indeferido', 'Encaminhado para a Secretaria'];

        $options = compact('courses', 'statuses', 'departments', 'discTypes', 'internal_statusOptions');
        return Inertia::render('ExportRequisitionsPage', ['label' => "Exportação de requerimentos", 'roleId' => $roleId, 'userRoles' => $user->roles, 'options' => $options]);
    }

    public function exportRequisitionsPost(Request $request)
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

        $exportData = $requisitions->map(function ($requisition) {
            $setToDepartment = $requisition->getRelation('events')->filter(function ($item) {
                return $item->type == 'Enviado para análise do departamento';
            })->last();

            $registeredAtJupiter = $requisition->getRelation('events')->filter(function ($item) {
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
                'Data de encaminhamento ao departamento/unidade' => $setToDepartment != null ? $setToDepartment->created_at->format('d-m-Y') : null,
                'Parecer' => $reviewIsEmpty ? null : $requisition->getRelation('reviews')[0]->reviewer_decision,
                'Parecerista' => $reviewIsEmpty ? null : $requisition->getRelation('reviews')[0]->reviewer_name,
                'Data do parecer' => $reviewIsEmpty ? null : $requisition->getRelation('reviews')[0]->updated_at->format('d-m-Y'),
                'Data do registro no Júpiter pelo Departamento' => $registeredAtJupiter != null ? $registeredAtJupiter->created_at->format('d-m-Y') : null
            ];

            return $data;
        });

        // dd($exportData);

        return new StreamedResponse(function () use ($exportData) {
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

    public function setRequisitionResult(Request $request) 
    {
        $this->checkUserUpdatePermission($request->requisitionId);
        if (!$this->hasRequisitionResultChanged($request))
        {
            abort(400, 'No changes were submitted.');
        }
        $requisition = Requisition::find($request->requisitionId);
        $resultType = $this->getResultEventTypeFrom($request);

        $requisition->result = $request->result;
        $requisition->result_text = $request->result_text;
        $requisition->situation = $resultType;
        $requisition->internal_status = $resultType;
        $requisition->save();

        $user = Auth::user();

        $event = new Event;
        $event->type = $resultType;
        $event->requisition_id = $request->requisitionId;
        $event->author_name = $user->name;
        $event->author_nusp = $user->codpes;
        $event->version = $requisition->latest_version;
        $event->message = $resultType;
        $event->save();

        return response('', 200)->header('Content-Type', 'text/plain');
    }

    private function hasRequisitionResultChanged($updateRequest)
    {
        $requisition = Requisition::find($updateRequest["requisitionId"]);

        $hasChanged = False;
        if ($requisition->result !== $updateRequest["result"]) {
            $hasChanged = True;
        }

        return $hasChanged;
    }

    private function getResultEventTypeFrom($updateRequest)
    {
        switch ($updateRequest["result"]) {
            case "Inconsistência nas informações":
                $resultType = EventType::BACK_TO_STUDENT;
                break;
            case "Deferido":
                $resultType = EventType::ACCEPTED;
                break;
            case "Indeferido":
                $resultType = EventType::REJECTED;
                break;
        }
        return $resultType;
    }

}
