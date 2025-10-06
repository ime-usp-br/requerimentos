<?php

namespace App\Http\Controllers;

use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Validator;
use Inertia\Inertia;
use App\Enums\RoleId;
use App\Enums\EventType;
use App\Enums\ResultType;
use App\Enums\DocumentType;
use App\Models\User;
use App\Models\Department;
use App\Models\DepartmentUserRole;
use App\Models\Document;
use App\Models\Event;
use App\Models\Requisition;
use App\Models\Review;
use App\Models\TakenDisciplines;
use App\Models\RequisitionsVersion;
use App\Http\Requests\RequisitionCreationRequest;
use App\Http\Requests\RequisitionUpdateRequest;
use App\Notifications\DepartmentNotification;
use App\Notifications\RequisitionResultNotification;
use App\Notifications\RegisteredNotification;
use App\Notifications\RequisitionCreatedNotification;
use App\Notifications\RequisitionUpdatedNotification;
use App\Exports\RequisitionsExport;
use Maatwebsite\Excel\Facades\Excel;
use Symfony\Component\HttpFoundation\StreamedResponse;
use Illuminate\Support\Facades\Log;


class RequisitionController extends Controller
{
    public function showRequisition($requisitionId)
    {
        $user = Auth::user();

        Log::info('RequisitionController::showRequisition - Requisition view requested', [
            'requisition_id' => $requisitionId,
            'user_codpes' => $user->codpes,
            'role_id' => $user->current_role_id
        ]);

        $requisition = Requisition::with('takenDisciplines', 'documents')->find($requisitionId);

        if (!$requisition) {
            Log::warning('RequisitionController::showRequisition - Requisition not found', [
                'requisition_id' => $requisitionId,
                'user_codpes' => $user->codpes
            ]);
            abort(404);
        }

        if ($user->current_role_id == RoleId::STUDENT && (int) $requisition->student_nusp !== (int) $user->codpes) {
            Log::warning('RequisitionController::showRequisition - Unauthorized access attempt', [
                'requisition_id' => $requisitionId,
                'user_codpes' => $user->codpes,
                'requisition_owner' => $requisition->student_nusp
            ]);
            abort(403);
        }

        $documents = $requisition->documents;

        $latestDocuments = [];
        foreach ($documents as $document) {
            $type = $document->type;
            if (!isset($latestDocuments[$type]) || $document->version > $latestDocuments[$type]->version) {
                $latestDocuments[$type] = $document;
            }
        }
        $latestDocumentsArray = array_values($latestDocuments);

        $takenDisciplines = $requisition->takenDisciplines;
        $maxVersions = [];
        foreach ($takenDisciplines as $disc) {
            $code = $disc->code ?? '';
            if (!isset($maxVersions[$code]) || $disc->version > $maxVersions[$code]->version) {
                $maxVersions[$code] = $disc;
            }
        }
        $latestTakenDisciplines = array_values($maxVersions);

        $roleId = $user->current_role_id;
        switch ($roleId) {
            case RoleId::STUDENT:
                $selectedActions =  [['edit_requisition']];
                break;
            case RoleId::SG:
                $selectedActions = [
                    [
                        'send_to_department',
                        'result'
                    ],
                    ['edit_requisition'],
                    [
                        'send_to_reviewers',
                        'reviews'
                    ],
                    ['requisition_history'],
                    [
                        'registered',
                        'automatic_requisition',
                        'export_current'
                    ]
                ];
                break;
            case RoleId::SECRETARY:
                $selectedActions =  [
                    [
                        'send_to_reviewers',
                        'reviews'
                    ],
                    ['requisition_history'],
                    ['registered']
                ];
                break;
            case RoleId::REVIEWER:
                $selectedActions = [['submit_review']];
                break;
        }

        Log::info('RequisitionController::showRequisition - Requisition data prepared successfully', [
            'requisition_id' => $requisitionId,
            'user_codpes' => $user->codpes,
            'role_id' => $roleId,
            'document_count' => count($latestDocumentsArray),
            'taken_disciplines_count' => count($latestTakenDisciplines)
        ]);

        return Inertia::render('RequisitionDetailPage', [
            'label' => 'Requerimentos',
            'selectedActions' => $selectedActions,
            'requisition' => $requisition->makeHidden(['documents', 'takenDisciplines']),
            'latestDocuments' => $latestDocumentsArray,
            'latestTakenDisciplines' => $latestTakenDisciplines
        ]);
    }

    public function newRequisitionGet()
    {
        $user = Auth::user();

        Log::info('RequisitionController::newRequisitionGet - New requisition form requested', [
            'user_codpes' => $user->codpes,
            'role_id' => $user->current_role_id
        ]);

        $response = Inertia::render('RequisitionFormPage', [
            'isStudent' => $user->current_role_id == RoleId::STUDENT,
            'label' => 'Novo Requerimento',
            'isUpdate' => false,
        ]);
        return $response;
    }

    public function newRequisitionPost(RequisitionCreationRequest $request)
    {
        $validatedRequest = $request->validated();
        $user = Auth::user();

        Log::info('RequisitionController::newRequisitionPost - New requisition creation started', [
            'user_codpes' => $user->codpes,
            'role_id' => $user->current_role_id,
            'student_nusp' => $validatedRequest['student_nusp'] ?? $user->codpes,
            'course' => $validatedRequest['course'],
            'requested_disc_code' => $validatedRequest['requestedDiscCode'],
            'department' => $validatedRequest['requestedDiscDepartment']
        ]);

        try {
            $requisitionId = null;
            $studentNusp = null;

            DB::transaction(function () use ($validatedRequest, $user, &$requisitionId, &$studentNusp) {
                $requisition = new Requisition;

                if ($user->current_role_id == 1) {
                    $requisition->student_nusp = $user->codpes;
                    $requisition->student_name = $user->name;
                    $requisition->email = $user->email;
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
                $requisition->result = ResultType::PENDING;
                $requisition->result_text = null;
                $requisition->observations = $validatedRequest["observations"] ?? "";
                $requisition->latest_version = 1;
                $requisition->editable = false;
                $requisition->save();

                // Capture values for logging
                $requisitionId = $requisition->id;
                $studentNusp = $requisition->student_nusp;

                $takenDiscsRecord = new Document;
                $takenDiscsRecord->path = $validatedRequest['takenDiscRecord']->store('documents');
                $takenDiscsRecord->requisition_id = $requisition->id;
                $takenDiscsRecord->type = DocumentType::TAKEN_DISCS_RECORD;
                $takenDiscsRecord->version = 1;
                $takenDiscsRecord->hash = hash_file('sha256', $validatedRequest['takenDiscRecord']->getRealPath());
                $takenDiscsRecord->save();

                $currentCourseRecord = new Document;
                $currentCourseRecord->path = $validatedRequest['courseRecord']->store('documents');
                $currentCourseRecord->requisition_id = $requisition->id;
                $currentCourseRecord->type = DocumentType::CURRENT_COURSE_RECORD;
                $currentCourseRecord->version = 1;
                $currentCourseRecord->hash = hash_file('sha256', $validatedRequest['courseRecord']->getRealPath());
                $currentCourseRecord->save();

                $takenDiscSyllabus = new Document;
                $takenDiscSyllabus->path = $validatedRequest['takenDiscSyllabus']->store('documents');
                $takenDiscSyllabus->requisition_id = $requisition->id;
                $takenDiscSyllabus->type = DocumentType::TAKEN_DISCS_SYLLABUS;
                $takenDiscSyllabus->version = 1;
                $takenDiscSyllabus->hash = hash_file('sha256', $validatedRequest['takenDiscSyllabus']->getRealPath());
                $takenDiscSyllabus->save();

                $requestedDiscSyllabus = new Document;
                $requestedDiscSyllabus->path = $validatedRequest['requestedDiscSyllabus']->store('documents');
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
                    $takenDisc->version = 1;
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

            if ($user->current_role_id != RoleId::SG) {
                $this->notifyRequisitionCreation();
            }

            Log::info('RequisitionController::newRequisitionPost - Requisition created successfully', [
                'user_codpes' => $user->codpes,
                'requisition_id' => $requisitionId,
                'student_nusp' => $studentNusp
            ]);

            return Inertia::location(route('list'));
        } catch (\Exception $e) {
            Log::error('RequisitionController::newRequisitionPost - Error creating new requisition', [
                'user_codpes' => $user->codpes,
                'error' => $e->getMessage(),
                'trace' => $e->getTraceAsString()
            ]);
            abort(500, $e->getMessage());
        }
    }

    private function notifyRequisitionCreation()
    {
        $sgUsers = DepartmentUserRole::getUsersWithRoleAndDepartment(RoleId::SG, null);

        foreach ($sgUsers as $sgUser) {
            if ($sgUser->email)
                $sgUser->notify(new RequisitionCreatedNotification());
        }
    }

    public function updateRequisitionGet($requisitionId)
    {
        $user = Auth::user();

        Log::info('RequisitionController::updateRequisitionGet - Update requisition form requested', [
            'requisition_id' => $requisitionId,
            'user_codpes' => $user->codpes,
            'role_id' => $user->current_role_id
        ]);

        $this->checkUserUpdatePermission($requisitionId);
        $requisition = Requisition::find($requisitionId);

        $latestTakenDisciplinesVersion = TakenDisciplines::where('requisition_id', $requisitionId)
            ->max('version') ?? 1;

        $latestTakenDisciplines = TakenDisciplines::where('requisition_id', $requisitionId)
            ->where('version', $latestTakenDisciplinesVersion)
            ->get();

        $documents = Document::where('requisition_id', $requisitionId)->get();
        $latestDocuments = [];
        foreach ($documents as $document) {
            $type = $document->type;
            if (!isset($latestDocuments[$type]) || $document->version > $latestDocuments[$type]->version) {
                $latestDocuments[$type] = $document;
            }
        }

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
            'takenDiscNames' => $latestTakenDisciplines->pluck('name')->toArray(),
            'takenDiscInstitutions' => $latestTakenDisciplines->pluck('institution')->toArray(),
            'takenDiscCodes' => $latestTakenDisciplines->pluck('code')->toArray(),
            'takenDiscYears' => $latestTakenDisciplines->pluck('year')->toArray(),
            'takenDiscGrades' => $latestTakenDisciplines->pluck('grade')->toArray(),
            'takenDiscSemesters' => $latestTakenDisciplines->pluck('semester')->toArray(),
            'takenDiscCount' => $latestTakenDisciplines->count(),
            'takenDiscRecord' => isset($latestDocuments[DocumentType::TAKEN_DISCS_RECORD]) ? [
                'id' => $latestDocuments[DocumentType::TAKEN_DISCS_RECORD]->id,
                'path' => $latestDocuments[DocumentType::TAKEN_DISCS_RECORD]->path,
                'url' => route('documents.view', $latestDocuments[DocumentType::TAKEN_DISCS_RECORD]->id),
                'version' => $latestDocuments[DocumentType::TAKEN_DISCS_RECORD]->version,
                'created_at' => $latestDocuments[DocumentType::TAKEN_DISCS_RECORD]->created_at
            ] : null,
            'courseRecord' => isset($latestDocuments[DocumentType::CURRENT_COURSE_RECORD]) ? [
                'id' => $latestDocuments[DocumentType::CURRENT_COURSE_RECORD]->id,
                'path' => $latestDocuments[DocumentType::CURRENT_COURSE_RECORD]->path,
                'url' => route('documents.view', $latestDocuments[DocumentType::CURRENT_COURSE_RECORD]->id),
                'version' => $latestDocuments[DocumentType::CURRENT_COURSE_RECORD]->version,
                'created_at' => $latestDocuments[DocumentType::CURRENT_COURSE_RECORD]->created_at
            ] : null,
            'takenDiscSyllabus' => isset($latestDocuments[DocumentType::TAKEN_DISCS_SYLLABUS]) ? [
                'id' => $latestDocuments[DocumentType::TAKEN_DISCS_SYLLABUS]->id,
                'path' => $latestDocuments[DocumentType::TAKEN_DISCS_SYLLABUS]->path,
                'url' => route('documents.view', $latestDocuments[DocumentType::TAKEN_DISCS_SYLLABUS]->id),
                'version' => $latestDocuments[DocumentType::TAKEN_DISCS_SYLLABUS]->version,
                'created_at' => $latestDocuments[DocumentType::TAKEN_DISCS_SYLLABUS]->created_at
            ] : null,
            'requestedDiscSyllabus' => isset($latestDocuments[DocumentType::REQUESTED_DISC_SYLLABUS]) ? [
                'id' => $latestDocuments[DocumentType::REQUESTED_DISC_SYLLABUS]->id,
                'path' => $latestDocuments[DocumentType::REQUESTED_DISC_SYLLABUS]->path,
                'url' => route('documents.view', $latestDocuments[DocumentType::REQUESTED_DISC_SYLLABUS]->id),
                'version' => $latestDocuments[DocumentType::REQUESTED_DISC_SYLLABUS]->version,
                'created_at' => $latestDocuments[DocumentType::REQUESTED_DISC_SYLLABUS]->created_at
            ] : null,
            'observations' => $requisition->observations,
        ];

        $user = Auth::user();

        Log::info('RequisitionController::updateRequisitionGet - Update form rendered successfully', [
            'requisition_id' => $requisitionId,
            'user_codpes' => $user->codpes,
            'role_id' => $user->current_role_id,
            'taken_disciplines_count' => $latestTakenDisciplines->count()
        ]);

        return Inertia::render('RequisitionFormPage', [
            'requisitionData' => $requisitionData,
            'label' => 'Novo Requerimento',
            'isStudent' => $user->current_role_id == RoleId::STUDENT,
            'isUpdate' => true,
        ]);
    }

    public function updateRequisitionPost(RequisitionUpdateRequest $request)
    {
        $validatedRequest = $request->validated();
        $user = Auth::user();

        Log::info('RequisitionController::updateRequisitionPost - Requisition update started', [
            'requisition_id' => $validatedRequest["requisitionId"],
            'user_codpes' => $user->codpes,
            'role_id' => $user->current_role_id
        ]);

        $this->checkUserUpdatePermission($validatedRequest["requisitionId"]);
        $requisition = Requisition::find($validatedRequest["requisitionId"]);

        $allDocumentTypes = [
            DocumentType::TAKEN_DISCS_RECORD,
            DocumentType::CURRENT_COURSE_RECORD,
            DocumentType::TAKEN_DISCS_SYLLABUS,
            DocumentType::REQUESTED_DISC_SYLLABUS
        ];

        $documentVersions = Document::where('requisition_id', $requisition->id)
            ->whereIn('type', $allDocumentTypes)
            ->get()
            ->groupBy('type')
            ->map(function ($documents) {
                return $documents->max('version');
            })
            ->toArray();

        foreach ($allDocumentTypes as $type) {
            if (!isset($documentVersions[$type])) {
                $documentVersions[$type] = 0;
            }
        }

        $takenDisciplinesVersion = TakenDisciplines::where('requisition_id', $requisition->id)
            ->max('version') ?? 0;

        $currentVersions = [
            'documents' => $documentVersions,
            'taken_disciplines' => $takenDisciplinesVersion
        ];

        $changedDocuments = $this->changedDocuments($requisition, $validatedRequest);
        $hasTakenDisciplinesChanged = $this->hasTakenDisciplinesChanged($validatedRequest);
        $hasRequisitionDataChanged = $this->hasRequisitionDataChanged($validatedRequest);
        $hasChanges = !empty($changedDocuments) || $hasTakenDisciplinesChanged || $hasRequisitionDataChanged;

        if ($hasChanges) {
            Log::info('RequisitionController::updateRequisitionPost - Changes detected, updating requisition', [
                'requisition_id' => $validatedRequest["requisitionId"],
                'user_codpes' => $user->codpes,
                'changed_documents_count' => count($changedDocuments),
                'taken_disciplines_changed' => $hasTakenDisciplinesChanged,
                'requisition_data_changed' => $hasRequisitionDataChanged
            ]);

            try {
                DB::transaction(function () use ($changedDocuments, $hasTakenDisciplinesChanged, $requisition, $validatedRequest, $currentVersions) {
                    $newVersions = $currentVersions;

                    foreach ($changedDocuments as $document) {
                        $newVersion = $currentVersions['documents'][$document['type']] + 1;
                        $this->updateDocumentData($requisition->id, $document['document'], $document['type'], $newVersion);
                        $newVersions['documents'][$document['type']] = $newVersion;
                    }

                    if ($hasTakenDisciplinesChanged) {
                        $newVersion = $currentVersions['taken_disciplines'] + 1;
                        $this->updateTakenDisciplinesData($validatedRequest, $newVersion);
                        $newVersions['taken_disciplines'] = $newVersion;
                    }

                    $this->updateRequisitionData($requisition, $validatedRequest, $currentVersions);

                    $user = Auth::user();
                    $requisition->refresh();
                    $event = new Event;
                    $event->type = $user->current_role_id == RoleId::STUDENT ? EventType::UPDATED_BY_STUDENT : EventType::UPDATED_BY_SG;
                    $event->requisition_id = $validatedRequest["requisitionId"];
                    $event->author_name = $user->name;
                    $event->author_nusp = $user->codpes;
                    $event->version = $requisition->latest_version;
                    $event->save();
                });

                $this->notifyRequisitionUpdate();

                Log::info('RequisitionController::updateRequisitionPost - Requisition updated successfully', [
                    'requisition_id' => $validatedRequest["requisitionId"],
                    'user_codpes' => $user->codpes,
                    'new_version' => $requisition->latest_version
                ]);
            } catch (\Exception $e) {
                Log::error('RequisitionController::updateRequisitionPost - Error updating requisition', [
                    'requisition_id' => $validatedRequest["requisitionId"],
                    'user_codpes' => $user->codpes,
                    'error' => $e->getMessage(),
                    'trace' => $e->getTraceAsString()
                ]);
                abort(500, $e->getMessage());
            }
        } else {
            Log::info('RequisitionController::updateRequisitionPost - No changes detected', [
                'requisition_id' => $validatedRequest["requisitionId"],
                'user_codpes' => $user->codpes
            ]);
        }

        return Inertia::location(route('list'));
    }

    private function checkUserUpdatePermission($requisitionId)
    {
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
        $latestDocuments = Document::where('requisition_id', $requisition->id)
            ->select('type', 'hash', 'version')
            ->whereIn('type', [
                DocumentType::TAKEN_DISCS_RECORD,
                DocumentType::CURRENT_COURSE_RECORD,
                DocumentType::TAKEN_DISCS_SYLLABUS,
                DocumentType::REQUESTED_DISC_SYLLABUS
            ])
            ->get()
            ->groupBy('type')
            ->map(function ($documents) {
                return $documents->sortByDesc('version')->first();
            });

        $newDocuments = [
            DocumentType::TAKEN_DISCS_RECORD => isset($updateRequest['takenDiscRecord']) && is_object($updateRequest['takenDiscRecord']) && method_exists($updateRequest['takenDiscRecord'], 'getRealPath') ? $updateRequest['takenDiscRecord'] : null,
            DocumentType::CURRENT_COURSE_RECORD => isset($updateRequest['courseRecord']) && is_object($updateRequest['courseRecord']) && method_exists($updateRequest['courseRecord'], 'getRealPath') ? $updateRequest['courseRecord'] : null,
            DocumentType::TAKEN_DISCS_SYLLABUS => isset($updateRequest['takenDiscSyllabus']) && is_object($updateRequest['takenDiscSyllabus']) && method_exists($updateRequest['takenDiscSyllabus'], 'getRealPath') ? $updateRequest['takenDiscSyllabus'] : null,
            DocumentType::REQUESTED_DISC_SYLLABUS => isset($updateRequest['requestedDiscSyllabus']) && is_object($updateRequest['requestedDiscSyllabus']) && method_exists($updateRequest['requestedDiscSyllabus'], 'getRealPath') ? $updateRequest['requestedDiscSyllabus'] : null,
        ];

        $changedDocuments = [];

        foreach ($newDocuments as $documentType => $newDocument) {
            if (!$newDocument) {
                continue;
            }

            $newDocumentHash = hash_file('sha256', $newDocument->getRealPath());

            if (isset($latestDocuments[$documentType])) {
                $existingDocument = $latestDocuments[$documentType];
                if ($existingDocument->hash !== $newDocumentHash) {
                    $changedDocuments[] = [
                        'type' => $documentType,
                        'document' => $newDocument
                    ];
                }
            } else {
                $changedDocuments[] = [
                    'type' => $documentType,
                    'document' => $newDocument
                ];
            }
        }
        return $changedDocuments;
    }

    private function hasTakenDisciplinesChanged($updateRequest)
    {
        $hasChanged = False;

        $latestVersion = TakenDisciplines::where('requisition_id', $updateRequest["requisitionId"])
            ->max('version') ?? 1;

        $existingTakenDisciplines = TakenDisciplines::where('requisition_id', $updateRequest["requisitionId"])
            ->where('version', $latestVersion)
            ->get();

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

        $sortFn = function ($a, $b) {
            return [$a['code'], $a['year']] <=> [$b['code'], $b['year']];
        };
        usort($existingTakenDisciplinesArray, $sortFn);
        usort($newTakenDisciplinesArray, $sortFn);

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
            $requisition->observations !== $updateRequest["observations"]
            || $requisition->department !== $updateRequest["requestedDiscDepartment"]
            || $requisition->requested_disc_type !== $updateRequest["requestedDiscType"]
        ) {
            $hasChanged = True;
        }

        return $hasChanged;
    }

    private function updateDocumentData($requisitionId, $documentData, $documentType, $newVersion)
    {
        $document = new Document;
        $document->path = $documentData->store('documents');
        $document->hash = hash_file('sha256', $documentData->getRealPath());
        $document->version = $newVersion;
        $document->requisition_id = $requisitionId;
        $document->type = $documentType;
        $document->save();
    }

    private function updateTakenDisciplinesData($updateRequest, $newVersion)
    {
        $requisitionId = $updateRequest["requisitionId"];

        for ($i = 0; $i < $updateRequest["takenDiscCount"]; $i++) {
            $takenDisc = new TakenDisciplines;
            $takenDisc->name = $updateRequest["takenDiscNames"][$i];
            $takenDisc->code = $updateRequest["takenDiscCodes"][$i] ?? "";
            $takenDisc->year = $updateRequest["takenDiscYears"][$i];
            $takenDisc->grade = $updateRequest["takenDiscGrades"][$i];
            $takenDisc->semester = $updateRequest["takenDiscSemesters"][$i];
            $takenDisc->institution = $updateRequest["takenDiscInstitutions"][$i];
            $takenDisc->requisition_id = $requisitionId;
            $takenDisc->version = $newVersion;
            $takenDisc->save();
        }
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

        $requisitionVersion->taken_disciplines_version = $versions['taken_disciplines'];
        $requisitionVersion->taken_disc_record_version = $versions['documents'][DocumentType::TAKEN_DISCS_RECORD];
        $requisitionVersion->course_record_version = $versions['documents'][DocumentType::CURRENT_COURSE_RECORD];
        $requisitionVersion->taken_disc_syllabus_version = $versions['documents'][DocumentType::TAKEN_DISCS_SYLLABUS];
        $requisitionVersion->requested_disc_syllabus_version = $versions['documents'][DocumentType::REQUESTED_DISC_SYLLABUS];

        $requisitionVersion->save();

        $requisition->requested_disc_type = $updateRequest['requestedDiscType'];
        $requisition->department = $updateRequest['requestedDiscDepartment'];
        $requisition->observations = $updateRequest["observations"];

        if (Auth::user()->current_role_id == RoleId::STUDENT) {
            $requisition->situation = EventType::RESENT_BY_STUDENT;
            $requisition->internal_status = EventType::RESENT_BY_STUDENT;
        }
        $requisition->result = "Sem resultado";
        $requisition->result_text = "";
        $requisition->latest_version = $requisition->latest_version + 1;
        $requisition->save();
    }

    private function notifyRequisitionUpdate()
    {
        $sgUsers = DepartmentUserRole::getUsersWithRoleAndDepartment(RoleId::SG, null);

        foreach ($sgUsers as $sgUser) {
            if ($sgUser->email)
                $sgUser->notify(new RequisitionUpdatedNotification());
        }
    }

    public function sendToDepartment(Request $request)
    {
        $requisitionId = $request['requisitionId'];
        $user = Auth::user();

        Log::info('RequisitionController::sendToDepartment - Sending requisition to department', [
            'requisition_id' => $requisitionId,
            'user_codpes' => $user->codpes,
            'role_id' => $user->current_role_id
        ]);

        DB::transaction(function () use ($request, $requisitionId) {
            $requisition = Requisition::find($requisitionId);

            $user = Auth::user();
            $event = new Event;
            $event->type = EventType::SENT_TO_DEPARTMENT;
            $event->requisition_id = $request['requisitionId'];
            $event->author_name = $user->name;
            $event->author_nusp = $user->codpes;
            $event->version = $requisition->latest_version;
            $event->save();

            $requisition->situation = EventType::SENT_TO_DEPARTMENT;
            $requisition->internal_status = EventType::SENT_TO_DEPARTMENT . " " .  $requisition->department;
            $requisition->registered = false;
            $requisition->save();
        });

        $this->notifyDepartment($requisitionId);

        Log::info('RequisitionController::sendToDepartment - Requisition sent to department successfully', [
            'requisition_id' => $requisitionId,
            'user_codpes' => $user->codpes
        ]);

        return response('', 200)->header('Content-Type', 'text/plain');
    }

    private function notifyDepartment($requisitionId)
    {
        $requisitionDepartment = Requisition::find($requisitionId)->department;
        $departmentId = Department::where('name', $requisitionDepartment)->first()->id;
        $departmentUsers = DepartmentUserRole::getUsersWithRoleAndDepartment(RoleId::SECRETARY, $departmentId);

        foreach ($departmentUsers as $departmentUser) {
            if ($departmentUser->email)
                $departmentUser->notify(new DepartmentNotification($departmentUser, $requisitionDepartment));
        }
    }

    public function automaticDeferral(Request $request)
    {
        $user = Auth::user();
        $requisitionId = $request['requisitionId'];

        Log::info('RequisitionController::automaticDeferral - Processing automatic deferral', [
            'requisition_id' => $requisitionId,
            'user_codpes' => $user->codpes,
            'role_id' => $user->current_role_id
        ]);

        DB::transaction(function () use ($request) {
            $user = Auth::user();

            // Atualiza a situação para "parecer deferido automaticamente"
            $requisition = Requisition::find($request['requisitionId']);
            $requisitionId = $request['requisitionId'];
            $requisition->situation = EventType::RETURNED_BY_REVIEWER;
            $requisition->internal_status = EventType::AUTOMATIC_DEFERRAL;
            $requisition->registered = false;
            $requisition->save();

            // Cria o parecer "deferido" e salva
            $review = new Review;
            $review->reviewer_name = $user->name;
            $review->reviewer_nusp = $user->codpes;
            $review->requisition_id = $requisitionId;
            $review->reviewer_decision = ResultType::ACCEPTED;
            $review->justification = 'Deferimento automático';
            $review->latest_version = 1;
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

        Log::info('RequisitionController::automaticDeferral - Automatic deferral processed successfully', [
            'requisition_id' => $requisitionId,
            'user_codpes' => $user->codpes
        ]);

        return response('', 200)->header('Content-Type', 'text/plain');
    }

    public function registered(Request $request)
    {
        $user = Auth::user();

        Log::info('RequisitionController::registered - Registering requisition in Jupiter', [
            'requisition_id' => $request->requisitionId,
            'user_codpes' => $user->codpes,
            'role_id' => $user->current_role_id
        ]);

        $this->checkUserRegisteredPermission($request->requisitionId);

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

        $this->notifyRegisted($request->requisitionId);

        Log::info('RequisitionController::registered - Requisition registered successfully', [
            'requisition_id' => $request->requisitionId,
            'user_codpes' => $user->codpes
        ]);

        return response('', 200)->header('Content-Type', 'text/plain');
    }

    private function checkUserRegisteredPermission($requisitionId)
    {
        $requisition = Requisition::find($requisitionId);

        $user = Auth::user();
        if (!$requisition) {
            abort(404);
        }
        if (($user->current_role_id != RoleID::SG) &&
            ($user->current_role_id != RoleID::SECRETARY)
        ) {
            abort(403);
        }

        if (($user->current_role_id == RoleID::SECRETARY) &&
            ($user->currentDepartment->name != $requisition->department)
        ) {
            abort(403);
        }
    }

    private function notifyRegisted($requisitionId)
    {
        $requisitionDepartment = Requisition::find($requisitionId)->department;
        $sgUsers = DepartmentUserRole::getUsersWithRoleAndDepartment(RoleId::SG, null);

        foreach ($sgUsers as $sgUser) {
            if ($sgUser->email)
                $sgUser->notify(new RegisteredNotification($requisitionDepartment));
        }
    }

    public function exportRequisitionsGet()
    {
        $user = Auth::user();

        Log::info('RequisitionController::exportRequisitionsGet - Export form requested', [
            'user_codpes' => $user->codpes,
            'role_id' => $user->current_role_id
        ]);

        $roleId = $user->current_role_id;

        $courses = Requisition::select('course')->distinct()->get();
        $statuses = Requisition::select('internal_status')->distinct()->get();

        $departments = ['Todos', 'MAC', 'MAE', 'MAT', 'MAP', 'Disciplina de fora do IME'];
        $discTypes = ['Todos', 'Obrigatória', 'Optativa Eletiva', 'Optativa Livre', 'Extracurricular'];
        $internal_statusOptions = ['Todos', 'Deferido', 'Indeferido', 'Encaminhado para a Secretaria'];

        $options = compact('courses', 'statuses', 'departments', 'discTypes', 'internal_statusOptions');

        Log::info('RequisitionController::exportRequisitionsGet - Export form rendered successfully', [
            'user_codpes' => $user->codpes,
            'role_id' => $user->current_role_id,
            'courses_count' => $courses->count(),
            'statuses_count' => $statuses->count()
        ]);

        return Inertia::render('ExportRequisitionsPage', ['label' => "Exportação de requerimentos", 'options' => $options]);
    }

    public function exportRequisitionsPost(Request $request)
    {
        $user = Auth::user();

        Log::info('RequisitionController::exportRequisitionsPost - Export requisitions started', [
            'user_codpes' => $user->codpes,
            'role_id' => $user->current_role_id,
            'request_data' => $request->all()
        ]);

        try {
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

            Log::info('RequisitionController::exportRequisitionsPost - Query executed', [
                'user_codpes' => $user->codpes,
                'found_requisitions' => $requisitions->count(),
                'filters' => [
                    'department' => $request->department,
                    'internal_status' => $request->internal_status,
                    'requested_disc_type' => $request->requested_disc_type,
                    'start_date' => $request->start_date,
                    'end_date' => $request->end_date
                ]
            ]);

            if ($requisitions->isEmpty()) {
                Log::warning('RequisitionController::exportRequisitionsPost - No requisitions found', [
                    'user_codpes' => $user->codpes,
                    'filters' => $request->all()
                ]);
                return response()->json(['message' => 'No requisitions found'], 404);
            }

            $exportData = $requisitions->map(function ($requisition, $index) use ($user) {
                try {
                    $sentToDepartment = $requisition->getRelation('events')->filter(function ($item) {
                        return $item->type == 'Enviado para análise do departamento';
                    })->last();

                    $registeredAtJupiter = $requisition->getRelation('events')->filter(function ($item) {
                        return $item->type == 'Aguardando avaliação da CG';
                    })->last();

                    $reviewIsEmpty = $requisition->getRelation('reviews')->isEmpty();

                    $reviews = '';
                    foreach ($requisition->getRelation('reviews') as $review) {
                        $reviews .= $review->reviewer_decision . ';';
                    }

                    $data = [
                        'Nome' => $requisition->student_name,
                        'Número USP' => $requisition->student_nusp,
                        'Curso' => $requisition->course,
                        'Data de abertura do Requerimento' => $requisition->created_at->format('d-m-Y'),
                        'Disciplina a ser dispensada' => $requisition->requested_disc_code,
                        'Departamento responsável' => $requisition->department,
                        'Situação' => $requisition->internal_status,
                        'Data de encaminhamento ao departamento/unidade' => $sentToDepartment != null ? $sentToDepartment->created_at->format('d-m-Y') : null,
                        'Parecer' => $reviewIsEmpty ? null : $reviews,
                        'Parecerista' => $reviewIsEmpty ? null : $requisition->getRelation('reviews')[0]->reviewer_name,
                        'Data do parecer' => $reviewIsEmpty ? null : $requisition->getRelation('reviews')[0]->updated_at->format('d-m-Y'),
                        'Data do registro no Júpiter pelo Departamento' => $registeredAtJupiter != null ? $registeredAtJupiter->created_at->format('d-m-Y') : null
                    ];

                    return $data;
                } catch (\Exception $e) {
                    Log::error("RequisitionController::exportRequisitionsPost - Export error while processing requisition {$index}", [
                        'user_codpes' => $user->codpes,
                        'requisition_id' => $requisition->id,
                        'error' => $e->getMessage(),
                        'trace' => $e->getTraceAsString()
                    ]);
                    throw $e;
                }
            });

            if ($exportData->isEmpty()) {
                Log::warning('RequisitionController::exportRequisitionsPost - Export data is empty after mapping', [
                    'user_codpes' => $user->codpes
                ]);
                return response()->json(['message' => 'No data to export'], 404);
            }

            $exportHandler = new RequisitionsExport($exportData);

            Log::info('RequisitionController::exportRequisitionsPost - Export file generated successfully', [
                'user_codpes' => $user->codpes,
                'exported_count' => $exportData->count()
            ]);

            return Excel::download($exportHandler, 'requisitions.xlsx');
        } catch (\Exception $e) {
            Log::error('RequisitionController::exportRequisitionsPost - Export requisitions failed', [
                'user_codpes' => $user->codpes,
                'error' => $e->getMessage(),
                'file' => $e->getFile(),
                'line' => $e->getLine(),
                'trace' => $e->getTraceAsString()
            ]);

            return response()->json([
                'error' => 'Export failed',
                'message' => $e->getMessage()
            ], 500);
        }
    }

    public function setRequisitionResult(Request $request)
    {
        $user = Auth::user();

        Log::info('RequisitionController::setRequisitionResult - Setting requisition result', [
            'requisition_id' => $request->requisitionId,
            'user_codpes' => $user->codpes,
            'role_id' => $user->current_role_id,
            'result' => $request->result
        ]);

        $this->checkUserUpdatePermission($request->requisitionId);

        $validator = Validator::make($request->all(), [
            'requisitionId' => 'required|exists:requisitions,id',
            'result' => 'required|string',
            'result_text' => 'required_if:result,' . ResultType::REJECTED . '|required_if:result,' . ResultType::CANCELLED . '|nullable|string',
        ]);

        if ($validator->fails()) {
            Log::warning('RequisitionController::setRequisitionResult - Validation failed', [
                'requisition_id' => $request->requisitionId,
                'user_codpes' => $user->codpes,
                'errors' => $validator->errors()->toArray()
            ]);
            return back()->withErrors($validator);
        }

        if (!$this->hasRequisitionResultChanged($request)) {
            Log::info('RequisitionController::setRequisitionResult - No changes detected', [
                'requisition_id' => $request->requisitionId,
                'user_codpes' => $user->codpes
            ]);
            return response('', 200)->header('Content-Type', 'text/plain');
        }

        $requisition = Requisition::find($request->requisitionId);

        $resultType = $this->getResultEventTypeFrom($request);

        $requisition->result = $request->result;
        $requisition->result_text = $request->result_text;
        $requisition->situation = $resultType;
        $requisition->internal_status = $resultType;
        $requisition->editable = $resultType == EventType::BACK_TO_STUDENT;
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

        $this->notifyRequisitionResult($requisition->student_nusp);

        Log::info('RequisitionController::setRequisitionResult - Requisition result set successfully', [
            'requisition_id' => $request->requisitionId,
            'user_codpes' => $user->codpes,
            'result' => $request->result,
            'result_type' => $resultType,
            'student_nusp' => $requisition->student_nusp
        ]);

        return response('', 200)->header('Content-Type', 'text/plain');
    }

    private function hasRequisitionResultChanged($updateRequest)
    {
        $requisition = Requisition::find($updateRequest["requisitionId"]);

        $hasChanged = False;
        if (
            $requisition->result !== $updateRequest["result"] ||
            $requisition->result_text !== $updateRequest["result_text"]
        ) {
            $hasChanged = True;
        }

        return $hasChanged;
    }

    private function getResultEventTypeFrom($updateRequest)
    {
        switch ($updateRequest["result"]) {
            case ResultType::INCONSISTENT:
                $resultType = EventType::BACK_TO_STUDENT;
                break;
            case ResultType::ACCEPTED:
                $resultType = EventType::ACCEPTED;
                break;
            case ResultType::REJECTED:
                $resultType = EventType::REJECTED;
                break;
            case ResultType::CANCELLED:
                $resultType = EventType::CANCELLED;
        }
        return $resultType;
    }

    private function notifyRequisitionResult($student_nusp)
    {
        $studentUser = User::where('codpes', $student_nusp)->first();
        if ($studentUser) {
            $studentUser->notify(new RequisitionResultNotification($studentUser));
        }
    }
}
