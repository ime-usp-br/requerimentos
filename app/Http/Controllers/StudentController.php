<?php

namespace App\Http\Controllers;

use App\Models\Event;
use App\Enums\EventType;
use App\Models\Document;
use App\Enums\DocumentType;
use App\Models\Requisition;
use Illuminate\Http\Request;
use App\Models\TakenDisciplines;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\Route;

class StudentController extends Controller
{
    public function list() {

        $user = Auth::user();

        $reqs = Requisition::with('takenDisciplines')->select('created_at', 'requested_disc', 'nusp', 'situation', 'id')->where('nusp', $user->codpes)->get();

        return view('pages.student.list', ['reqs' => $reqs]);
    }

    public function show($requisitionId) {
        $req = Requisition::with('takenDisciplines', 'documents')->find($requisitionId);
        $user = Auth::user();

        if (!$req || $req->nusp !== $user->codpes) {
            abort(404);
        }

        $routeName = Route::currentRouteName();

        if ($routeName === 'student.show') {
            $documents = $req->documents->sortByDesc('created_at');
            // dd($req->documents);

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
        } elseif ($routeName === 'student.edit') {
            return view('pages.student.editRequisition', ['req' => $req, 'takenDiscs' => $req->takenDisciplines]);
        }
    }

    public function create(Request $request) {
        $user = Auth::user();

        $takenDiscCount = (int) $request->takenDiscCount;
        $discsArray = [];

        for ($i = 1; $i <= $takenDiscCount; $i++) {
            $discsArray["disc$i-name"] = 'required | max:255';
            $discsArray["disc$i-code"] = 'max:255';
            $discsArray["disc$i-year"] = 'required | numeric | integer';
            $discsArray["disc$i-grade"] = 'required | numeric';
            $discsArray["disc$i-semester"] = 'required';
            $discsArray["disc$i-institution"] = 'required | max:255';
        }

        $inputArray = [
            'course' => 'required | max:255',
            'requested-disc-name' => 'required | max:255',
            'requested-disc-type' => 'required',
            'requested-disc-code' => 'required | max:255',
            // essas regras de validação dos arquivos tem que ser colocadas nessa ordem
            // (com o mimes:pdf no final), senão da ruim 
            'taken-disc-record' => 'required | file | max:2048 | mimes:pdf',
            'course-record' => 'required | file | max:2048 | mimes:pdf',
            'taken-disc-syllabus' => 'required | file | max:2048 | mimes:pdf',
            'requested-disc-syllabus' => 'required | file | max:2048 | mimes:pdf',
            'disc-department' => 'required'
        ];

        $data = $request->validate(array_merge($inputArray, $discsArray));

        $req = new Requisition;
        $req->department = $data['disc-department'];
        $req->nusp = $user->codpes;
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
        $req->taken_discs_record = $request->file('taken-disc-record')->store('test');
        $req->current_course_record = $request->file('course-record')->store('test');
        $req->taken_discs_syllabus = $request->file('taken-disc-syllabus')->store('test');
        $req->requested_disc_syllabus = $request->file('requested-disc-syllabus')->store('test');
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


        for ($i = 1; $i <= $takenDiscCount; $i++) {
            $takenDisc = new TakenDisciplines;
            $takenDisc->name = $data["disc$i-name"];
            $takenDisc->code = $data["disc$i-code"] ?? "";
            $takenDisc->year = $data["disc$i-year"];
            $takenDisc->grade = number_format((float) $data["disc$i-grade"], 2, '.', '');
            $takenDisc->semester = $data["disc$i-semester"];
            $takenDisc->institution = $data["disc$i-institution"];
            $takenDisc->requisition_id = $req->id;
            $takenDisc->save();
        }

        $event = new Event;
        $event->type = EventType::SENT_TO_SG;
        $event->requisition_id = $req->id;
        $event->author_name = $user->name; 
        $event->author_nusp = $user->codpes;
        $event->save();

        return redirect()->route('student.newRequisition')->with('success', ['title message' => 'Requerimento criado', 'body message' => "O requerimento foi criado com sucesso. Acompanhe o andamento pelo campo 'situação' na página inicial."]);
    }

    public function update(Request $request, $requisitionId) {

        $reqToBeUpdated = Requisition::find($requisitionId);
        $user = Auth::user();

        if (!$reqToBeUpdated || $reqToBeUpdated->nusp !== $user->codpes || $reqToBeUpdated->result !== 'Inconsistência nas informações') {
            abort(403);
        }

        $takenDiscCount = (int) $request->takenDiscCount;

        $discsArray = [];

        for ($i = 1; $i <= $takenDiscCount; $i++) {
            $discsArray["disc$i-name"] = 'required | max:255';
            $discsArray["disc$i-code"] = 'max:255';
            $discsArray["disc$i-year"] = 'required | numeric | integer';
            $discsArray["disc$i-grade"] = 'required | numeric';
            $discsArray["disc$i-semester"] = 'required';
            $discsArray["disc$i-institution"] = 'required';
        }

        $inputArray = [
            'course' => 'required | max:255',
            'requested-disc-name' => 'required | max:255',
            'requested-disc-type' => 'required',
            'requested-disc-code' => 'required | max:255',
            'disc-department' => 'required',
            'taken-disc-record' => 'required | file | mimes:pdf',
            'course-record' => 'required | file | mimes:pdf',
            'taken-disc-syllabus' => 'required | file | mimes:pdf',
            'requested-disc-syllabus' => 'required | file | mimes:pdf',
        ];

        $data = $request->validate(array_merge($inputArray, $discsArray));
        
        $reqToBeUpdated->department = $data['disc-department'];
        $reqToBeUpdated->course = $data['course'];
        $reqToBeUpdated->requested_disc = $data['requested-disc-name'];
        $reqToBeUpdated->requested_disc_type = $data['requested-disc-type'];
        $reqToBeUpdated->requested_disc_code = $data['requested-disc-code'];
        $reqToBeUpdated->observations = request('observations');
        $reqToBeUpdated->taken_discs_record = $request->file('taken-disc-record')->store('test');
        $reqToBeUpdated->current_course_record = $request->file('course-record')->store('test');
        $reqToBeUpdated->taken_discs_syllabus = $request->file('taken-disc-syllabus')->store('test');
        $reqToBeUpdated->requested_disc_syllabus = $request->file('requested-disc-syllabus')->store('test');
        $reqToBeUpdated->situation = EventType::RESEND_BY_STUDENT;
        $reqToBeUpdated->internal_status = EventType::RESEND_BY_STUDENT;
        $reqToBeUpdated->result = 'Sem resultado';
        $reqToBeUpdated->save();

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

        for ($i = 1; $i <= $takenDiscCount; $i++) {
            $takenDisc = TakenDisciplines::find(request("disc$i-id"));
            $takenDisc->name = $data["disc$i-name"];
            $takenDisc->code = $data["disc$i-code"] ?? "";
            $takenDisc->year = $data["disc$i-year"];
            $takenDisc->grade = $data["disc$i-grade"];
            $takenDisc->semester = $data["disc$i-semester"];
            $takenDisc->institution = $data["disc$i-institution"];
            $takenDisc->requisition_id = $requisitionId;
            $takenDisc->save();
        }

        $event = new Event;
        $event->type = EventType::RESEND_BY_STUDENT;
        $event->requisition_id = $requisitionId;
        $event->author_name = Auth::user()->name; 
        $event->author_nusp = Auth::user()->codpes;
        $event->save();

        return redirect()->route('student.edit', ['requisitionId' => $requisitionId])->with('success', ['title message' => 'Requerimento salvo', 'body message' => 'As novas informações do requerimento foram salvas com sucesso']);
    }
}
