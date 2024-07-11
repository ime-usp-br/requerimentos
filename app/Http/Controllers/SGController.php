<?php

namespace App\Http\Controllers;

use App\Models\User;
use App\Models\Event;
use App\Enums\EventType;
use App\Models\Document;
use App\Enums\DocumentType;
use App\Models\Requisition;
use App\Models\TakenDisciplines;
use Illuminate\Support\Facades\DB;
// use Illuminate\Http\Request;
use App\Models\RequisitionsVersion;

use Illuminate\Support\Facades\Auth;
use App\Models\TakenDisciplinesVersion;
use App\Http\Requests\RequisitionUpdateRequest;
use App\Http\Requests\RequisitionCreationRequest;

// use Illuminate\Support\Facades\Auth;
// use Illuminate\Support\Facades\Route;

class SGController extends Controller
{
    public function list() {
        $selectedColumns = ['created_at', 'student_name', 'nusp', 'internal_status', 'department', 'id'];

        $reqs = Requisition::select($selectedColumns)->get();

        return view('pages.sg.list', ['reqs' => $reqs]);
    }

    public function show($requisitionId) {
        $req = Requisition::with('takenDisciplines', 'documents')->find($requisitionId);
        
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
        return view('pages.sg.detail', ['req' => $req, 'takenDiscs' => $req->takenDisciplines, 'takenDiscsRecords' => $takenDiscsRecords, 'currentCourseRecords' => $currentCourseRecords, 'takenDiscSyllabi' => $takenDiscSyllabi, 'requestedDiscSyllabi' => $requestedDiscSyllabi]);
    }

    public function create(RequisitionCreationRequest $request) {

        $data = $request->validated();

        DB::transaction(function() use ($data, $request) {

            $req = new Requisition;
            $req->department = $data['disc-department'];
            $req->nusp = $data['nusp'];
            $req->student_name = $data['name'];
            $req->email = $data['email'];
            $req->course = $data['course'];
            $req->requested_disc = $data['requested-disc-name'];
            $req->requested_disc_type = $data['requested-disc-type'];
            $req->requested_disc_code = $data['requested-disc-code'];
            $req->situation = EventType::SENT_TO_SG;
            $req->internal_status = EventType::SENT_TO_SG;
            $req->result = 'Sem resultado';
            $req->result_text = null;
            $req->validated = False;
            $req->latest_version = 1;
            $req->observations = $request->observations;

            $req->save();

            $takenDiscsRecord = new Document;
            $takenDiscsRecord->path = $request->file('taken-disc-record')->store('test');
            $takenDiscsRecord->requisition_id = $req->id;
            $takenDiscsRecord->type = DocumentType::TAKEN_DISCS_RECORD;
            $takenDiscsRecord->save();

            $currentCourseRecord = new Document;
            $currentCourseRecord->path = $request->file('course-record')->store('test');
            $currentCourseRecord->requisition_id = $req->id;
            $currentCourseRecord->type = DocumentType::CURRENT_COURSE_RECORD;
            $currentCourseRecord->save();

            $takenDiscSyllabus = new Document;
            $takenDiscSyllabus->path = $request->file('taken-disc-syllabus')->store('test');
            $takenDiscSyllabus->requisition_id = $req->id;
            $takenDiscSyllabus->type = DocumentType::TAKEN_DISCS_SYLLABUS;
            $takenDiscSyllabus->save();

            $requestedDiscSyllabus = new Document;
            $requestedDiscSyllabus->path = $request->file('requested-disc-syllabus')->store('test');
            $requestedDiscSyllabus->requisition_id = $req->id;
            $requestedDiscSyllabus->type = DocumentType::REQUESTED_DISC_SYLLABUS;
            $requestedDiscSyllabus->save();

            for ($i = 1; $i <= $request->takenDiscCount; $i++) {
                $takenDisc = new TakenDisciplines;
                $takenDisc->name = $data["disc$i-name"];
                $takenDisc->code = $data["disc$i-code"] ?? "";
                $takenDisc->year = $data["disc$i-year"];
                $takenDisc->grade = number_format((float) $data["disc$i-grade"], 2, '.', '');
                $takenDisc->semester = $data["disc$i-semester"];
                $takenDisc->institution = $data["disc$i-institution"];
                $takenDisc->requisition_id = $req->id;
                $takenDisc->latest_version = 1;
                $takenDisc->save();
            }

            $event = new Event;
            $event->type = EventType::SENT_TO_SG;
            $event->requisition_id = $req->id;
            
            $event->author_name = Auth::user()->name; 
            $event->author_nusp = Auth::user()->codpes;
            $event->version = 1;
            $event->save();
        });

        return redirect()->route('sg.newRequisition')->with('success', ['title message' => 'Requerimento criado', 'body message' => 'O requerimento foi criado com sucesso. Acompanhe o andamento pela página inicial.']);
    }

    public function update(RequisitionUpdateRequest $request, $requisitionId) {
        
        $data = $request->validated();

        $requisitionData = $request->getRequisitionData();
        $takenDisciplinesData = $request->getDisciplinesData();

        DB::transaction(function() use ($requisitionData, 
                                        $takenDisciplinesData, 
                                        $request, 
                                        $requisitionId) {

            $reqToBeUpdated = Requisition::find($requisitionId);

            // vendo se eu preciso de fato atualizar as tabelas
            $someFieldWasChanged = False;

            foreach($requisitionData as $key => $value) {
                if ($reqToBeUpdated->$key !== $value) {
                    $someFieldWasChanged = True;
                    break;
                }
            }

            for ($i = 1; $i <= $request->takenDiscCount; $i++) {

                $takenDisc = TakenDisciplines::find(request("disc$i-id"));

                foreach($takenDisc->getAttributes() as $key => $value) {

                    if (isset($takenDisciplinesData["disc$i-" . $key]) && 
                        $takenDisciplinesData["disc$i-" . $key] !== $value) {
                        $someFieldWasChanged = True;
                        break 2;
                    }
                }
            }

            if (!$someFieldWasChanged) {

                if ($request->button === 'department') {
                    $event = new Event;
                    $event->type = EventType::SENT_TO_DEPARTMENT;
                    $event->requisition_id = $requisitionId;
                    $event->author_name = Auth::user()->name; 
                    $event->author_nusp = Auth::user()->codpes;
                    $event->version = $reqToBeUpdated->latest_version;
                    $event->save();

                    $reqToBeUpdated->situation = EventType::SENT_TO_DEPARTMENT;
                    $reqToBeUpdated->internal_status = EventType::SENT_TO_DEPARTMENT;
                    $reqToBeUpdated->validated = true;
                    $reqToBeUpdated->save();
                }

                return;
            }

            // criando uma versão nova na tabela de versões
            $newReqVersion = new RequisitionsVersion;
            $fields = $reqToBeUpdated->toArray();
            unset($fields['latest_version'], 
                  $fields['id'], 
                  $fields['created_at'], 
                  $fields['updated_at'],
                  $fields['situation'], 
                  $fields['internal_status'], 
                  $fields['validated']);
            $newReqVersion->fill($fields);
            $newReqVersion->requisition_id = $reqToBeUpdated->id;
            $newReqVersion->version = $reqToBeUpdated->latest_version;
            $newReqVersion->save(); 


            // atualizando a versão mais recente
            $reqToBeUpdated->latest_version = $reqToBeUpdated->latest_version + 1;

            if ($reqToBeUpdated->result !== $requisitionData['result']) {

                if ($requisitionData['result'] === 'Inconsistência nas informações') {
                    $type = EventType::BACK_TO_STUDENT;
                } elseif ($requisitionData['result'] === 'Deferido') {
                    $type = EventType::ACCEPTED;
                } elseif ($requisitionData['result'] === 'Indeferido') {
                    $type = EventType::REJECTED;
                } elseif ($requisitionData['result'] === 'Sem resultado') {
                    $type = EventType::IN_REVALUATION;
                }

                $event = new Event;
                $event->type = $type;
                $event->requisition_id = $requisitionId;
                $event->author_name = Auth::user()->name; 
                $event->author_nusp = Auth::user()->codpes;
                $event->version = $reqToBeUpdated->latest_version;
                
                $reqToBeUpdated->situation = $type;
                $reqToBeUpdated->internal_status = $type;
                $event->save();
            } 

            if ($request->button === 'department') {

                $event = new Event;
                $event->type = EventType::SENT_TO_DEPARTMENT;
                $event->requisition_id = $requisitionId;
                $event->author_name = Auth::user()->name; 
                $event->author_nusp = Auth::user()->codpes;
                $event->version = $reqToBeUpdated->latest_version;
                $event->save();

                $reqToBeUpdated->situation = EventType::SENT_TO_DEPARTMENT;
                $reqToBeUpdated->internal_status = EventType::SENT_TO_DEPARTMENT;
                $reqToBeUpdated->validated = true;
            }

            $reqToBeUpdated->fill($requisitionData);
            $reqToBeUpdated->save();

            for ($i = 1; $i <= $request->takenDiscCount; $i++) {

                $takenDisc = TakenDisciplines::find(request("disc$i-id"));

                // criando as versões das disciplinas
                $newDiscVersion = new TakenDisciplinesVersion;
                $fields = $takenDisc->toArray();
                unset($fields['latest_version'], 
                      $fields['id'], 
                      $fields['created_at'], 
                      $fields['updated_at']);
                $newDiscVersion->fill($fields);
                $newDiscVersion->version = $reqToBeUpdated->latest_version - 1;
                $newDiscVersion->save();

                // atualizando a versão mais recente
                $takenDisc->name = $takenDisciplinesData["disc$i-name"];
                $takenDisc->code = $takenDisciplinesData["disc$i-code"] ?? "";
                $takenDisc->year = $takenDisciplinesData["disc$i-year"];
                $takenDisc->grade = $takenDisciplinesData["disc$i-grade"];
                $takenDisc->semester = $takenDisciplinesData["disc$i-semester"];
                $takenDisc->institution = $takenDisciplinesData["disc$i-institution"];
                $takenDisc->requisition_id = $reqToBeUpdated->id;
                $takenDisc->latest_version = $reqToBeUpdated->latest_version;
                $takenDisc->save();
            }
            
        });

        if ($request->button === 'reviewer') {
            return redirect()->route('reviewer.reviewerPick', ['requisitionId' => $requisitionId]);
        } elseif ($request->button === 'department') {
            $bodyMsg = 'As informações do requerimento foram salvas e enviadas para o departamento';
            $titleMsg = 'Requerimento enviado';     
        } elseif ($request->button === 'save') {
            $bodyMsg = 'As informações do requerimento foram salvas';
            $titleMsg = 'Requerimento salvo';     
        }
        
        return redirect()->route('sg.show', ['requisitionId' => $requisitionId])->with('success', ['title message' => $titleMsg, 'body message' => $bodyMsg]);
    }

    public function users() {
        $selectedColumns = ['name', 'codpes', 'id'];

        $usersWithRoles = User::whereHas('roles')->select($selectedColumns)->get();
        return view('pages.sg.users', ['users' => $usersWithRoles]);
    }

}
