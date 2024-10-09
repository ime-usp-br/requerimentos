<?php

namespace App\Http\Controllers;

use App\Models\Event;
use App\Enums\EventType;
use App\Models\Document;
use App\Enums\DocumentType;
use App\Models\Requisition;
use App\Models\TakenDisciplines;
use Illuminate\Support\Facades\DB;
use App\Models\RequisitionsVersion;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\Route;
use App\Models\TakenDisciplinesVersion;
use Illuminate\Support\Facades\Session;
use App\Http\Requests\RequisitionUpdateRequest;
use App\Http\Requests\RequisitionCreationRequest;

class StudentController extends Controller
{
    public function list() {

        $user = Auth::user();

        $reqs = Requisition::with('takenDisciplines')->select('created_at', 'requested_disc', 'student_nusp', 'situation', 'id')->where('student_nusp', $user->codpes)->get();

        return view('pages.student.list', ['reqs' => $reqs]);
    }

    public function show($requisitionId) {
        
        $req = Requisition::with('takenDisciplines', 'documents')->find($requisitionId);
        $user = Auth::user();

        // o cast para int foi adicionado porque o banco sqlite3 retorna 
        // $req->student_nusp como uma string no server de produção. Sem esse cast,
        // os testes falham dentro do server
        if (!$req || (int) $req->student_nusp !== $user->codpes) {
            abort(404);
        }

        $routeName = Route::currentRouteName();

        if ($routeName === 'student.show') {
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

            return view('pages.student.detail', ['req' => $req, 'takenDiscs' => $req->takenDisciplines, 'takenDiscsRecords' => $takenDiscsRecords, 'currentCourseRecords' => $currentCourseRecords, 'takenDiscSyllabi' => $takenDiscSyllabi, 'requestedDiscSyllabi' => $requestedDiscSyllabi]);
        } elseif ($routeName === 'student.edit' && $req->result === 'Inconsistência nas informações' || Session::has('success')) {
            return view('pages.student.editRequisition', ['req' => $req, 'takenDiscs' => $req->takenDisciplines]);
        } else {
            abort(403);
        }
    }

    public function create(RequisitionCreationRequest $request) {
        
        $data = $request->validated();

        DB::transaction(function() use ($data, $request) {
            $user = Auth::user();

            $req = new Requisition;
            $req->department = $data['disc-department'];
            $req->student_nusp = $user->codpes;
            $req->student_name = $user->name;
            $req->email = $user->email;
            $req->course = $data['course'];
            $req->requested_disc = $data['requested-disc-name'];
            $req->requested_disc_type = $data['requested-disc-type'];
            $req->requested_disc_code = $data['requested-disc-code'];
            $req->situation = EventType::SENT_TO_SG;
            $req->internal_status = EventType::SENT_TO_SG;
            $req->result = 'Sem resultado';
            $req->result_text = null;
            $req->observations = $request->observations;
            $req->validated = False;
            $req->latest_version = 1;
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
            $event->author_name = $user->name; 
            $event->author_nusp = $user->codpes;
            $event->version = 1;
            $event->save();
        });
        

        return redirect()
               ->route('student.newRequisition')
               ->with('success', 
               [
                'title message' => 'Requerimento criado', 
                'body message' => "O requerimento foi criado com sucesso. Acompanhe o andamento pelo campo 'situação' na página inicial."
               ]);
    }

    public function update(RequisitionUpdateRequest $request, $requisitionId) {

        $reqToBeUpdated = Requisition::find($requisitionId);
        $requisitionData = $request->getRequisitionData();
        $takenDisciplinesData = $request->getDisciplinesData();
        
        DB::transaction(function() use ($requisitionData, 
                                        $takenDisciplinesData,
                                        $request, 
                                        $reqToBeUpdated, 
                                        $requisitionId) {
            // salvando os documentos
            $takenDiscsRecord = new Document;
            $takenDiscsRecord->path = $request->file('taken-disc-record')->store('test');
            $takenDiscsRecord->requisition_id = $reqToBeUpdated->id;
            $takenDiscsRecord->type = DocumentType::TAKEN_DISCS_RECORD;
            $takenDiscsRecord->save();
            
            $currentCourseRecord = new Document;
            $currentCourseRecord->path = $request->file('course-record')->store('test');
            $currentCourseRecord->requisition_id = $reqToBeUpdated->id;
            $currentCourseRecord->type = DocumentType::CURRENT_COURSE_RECORD;
            $currentCourseRecord->save();

            $takenDiscSyllabus = new Document;
            $takenDiscSyllabus->path = $request->file('taken-disc-syllabus')->store('test');
            $takenDiscSyllabus->requisition_id = $reqToBeUpdated->id;
            $takenDiscSyllabus->type = DocumentType::TAKEN_DISCS_SYLLABUS;
            $takenDiscSyllabus->save();

            $requestedDiscSyllabus = new Document;
            $requestedDiscSyllabus->path = $request->file('requested-disc-syllabus')->store('test');
            $requestedDiscSyllabus->requisition_id = $reqToBeUpdated->id;
            $requestedDiscSyllabus->type = DocumentType::REQUESTED_DISC_SYLLABUS;
            $requestedDiscSyllabus->save();
            
            // logando o evento do retorno do estudante
            $event = new Event;
            $event->type = EventType::RESEND_BY_STUDENT;
            $event->requisition_id = $requisitionId;
            $event->author_name = Auth::user()->name; 
            $event->author_nusp = Auth::user()->codpes;

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

                $reqToBeUpdated->situation = EventType::RESEND_BY_STUDENT;
                $reqToBeUpdated->internal_status = EventType::RESEND_BY_STUDENT;
                $reqToBeUpdated->save();

                $event->version = $reqToBeUpdated->latest_version;
                $event->save();

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

            // dd($requisitionData);
            // atualizando a versão mais recente na tabela principal
            $reqToBeUpdated->fill($requisitionData);

            $reqToBeUpdated->observations = request('observations');
            $reqToBeUpdated->situation = EventType::RESEND_BY_STUDENT;
            $reqToBeUpdated->internal_status = EventType::RESEND_BY_STUDENT;
            $reqToBeUpdated->result = 'Sem resultado';
            $reqToBeUpdated->latest_version = $reqToBeUpdated->latest_version + 1;
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

            $event->version = $reqToBeUpdated->latest_version;
            $event->save();

        });
        
        return redirect()->route('student.edit', ['requisitionId' => $requisitionId])->with('success', ['title message' => 'Requerimento salvo', 'body message' => 'As novas informações do requerimento foram salvas com sucesso']);
    }
}
