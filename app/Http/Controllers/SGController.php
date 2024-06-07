<?php

namespace App\Http\Controllers;

use App\Enums\DocumentType;
use App\Models\Document;
use App\Models\User;
use App\Models\Event;
use App\Enums\RoleName;
use App\Enums\EventType;
use App\Models\Requisition;
use Illuminate\Http\Request;
use App\Models\TakenDisciplines;
use Spatie\Permission\Models\Role;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\DB;


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
        return view('pages.sg.detail', ['req' => $req, 'takenDiscs' => $req->takenDisciplines, 'takenDiscsRecords' => $takenDiscsRecords, 'currentCourseRecords' => $currentCourseRecords, 'takenDiscSyllabi' => $takenDiscSyllabi, 'requestedDiscSyllabi' => $requestedDiscSyllabi]);
    }

    public function readOnlyShow($requisitionId) {
        $req = Requisition::with('takenDisciplines')->find($requisitionId);
        
        return view('pages.sg.detail', ['req' => $req, 'takenDiscs' => $req->takenDisciplines]);
    }

    public function editableShow($requisitionId) {
        $req = Requisition::with('takenDisciplines')->find($requisitionId);
        
        return view('pages.sg.detail', ['req' => $req, 'takenDiscs' => $req->takenDisciplines]);
    }

    public function create(Request $request) {
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
            'name' => 'required | max:255',
            'email' => 'required | max:255 | email ',
            'nusp' => 'required | numeric | integer',
            'course' => 'required | max:255',
            'requested-disc-name' => 'required | max:255',
            'requested-disc-type' => 'required',
            'requested-disc-code' => 'required',
            'taken-disc-record' => 'required | file | mimes:pdf',
            'course-record' => 'required | file | mimes:pdf',
            'taken-disc-syllabus' => 'required | file | mimes:pdf',
            'requested-disc-syllabus' => 'required | file | mimes:pdf',
            'disc-department' => 'required'
        ];

        $data = $request->validate(array_merge($inputArray, $discsArray));

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
        // $req->reviewer_name = null;
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
            $takenDisc->grade = $data["disc$i-grade"];
            $takenDisc->semester = $data["disc$i-semester"];
            $takenDisc->institution = $data["disc$i-institution"];
            $takenDisc->requisition_id = $req->id;
            $takenDisc->save();
        }

        $event = new Event;
        $event->type = EventType::SENT_TO_SG;
        $event->requisition_id = $req->id;
        
        $event->author_name = Auth::user()->name; 
        $event->author_nusp = Auth::user()->codpes;
        $event->save();

        return redirect()->route('sg.newRequisition')->with('success', ['title message' => 'Requerimento criado', 'body message' => 'O requerimento foi criado com sucesso. Acompanhe o andamento pela página inicial.']);
    }

    public function update($requisitionId, Request $request) {
        // dd($request);
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
            'name' => 'required | max:255',
            'email' => 'required | max:255 | email ',
            'nusp' => 'required | numeric | integer',
            'course' => 'required | max:255',
            'requested-disc-name' => 'required | max:255',
            'requested-disc-type' => 'required',
            'requested-disc-code' => 'required',
            'disc-department' => 'required'
        ];

        $data = $request->validate(array_merge($inputArray, $discsArray));
        
        $reqToBeUpdated = Requisition::find($requisitionId);
        $reqToBeUpdated->department = $data['disc-department'];
        $reqToBeUpdated->nusp = $data['nusp'];
        $reqToBeUpdated->student_name = $data['name'];
        $reqToBeUpdated->email = $data['email'];
        $reqToBeUpdated->course = $data['course'];
        $reqToBeUpdated->requested_disc = $data['requested-disc-name'];
        $reqToBeUpdated->requested_disc_type = $data['requested-disc-type'];
        $reqToBeUpdated->requested_disc_code = $data['requested-disc-code'];
        
        if ($reqToBeUpdated->result !== request('result')) {
            $reqToBeUpdated->result = request('result');

            $user = Auth::user();
            if (request('result') === 'Inconsistência nas informações') {
                $type = EventType::BACK_TO_STUDENT;
            } elseif (request('result') === 'Deferido') {
                $type = EventType::ACCEPTED;
            } elseif (request('result') === 'Indeferido') {
                $type = EventType::REJECTED;
            } elseif (request('result') === 'Sem resultado') {
                $type = EventType::IN_REVALUATION;
            }

            $event = new Event;
            $event->type = $type;
            $event->requisition_id = $requisitionId;
            $event->author_name = Auth::user()->name; 
            $event->author_nusp = Auth::user()->codpes;
            $reqToBeUpdated->situation = $type;
            $reqToBeUpdated->internal_status = $type;
            $event->save();
        }

        $reqToBeUpdated->result_text = request('result-text');
        $reqToBeUpdated->observations = request('observations');
        $reqToBeUpdated->save();

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

        if ($request->button === 'send') {
            // $bodyMsg = 'O requerimento foi enviado para o departamento';
            // $titleMsg = 'Requerimento enviado';
            return redirect()->route('sg.reviewerPick', ['requisitionId' => $requisitionId]);
        } elseif ($request->button === 'save') {
            $bodyMsg = 'As informações do requerimento foram salvas';
            $titleMsg = 'Requerimento salvo';     
            return redirect()->route('sg.show', ['requisitionId' => $requisitionId])->with('success', ['title message' => $titleMsg, 'body message' => $bodyMsg]);   
        }
    }

    public function users() {
        $selectedColumns = ['name', 'codpes', 'id'];

        $usersWithRoles = User::whereHas('roles')->select($selectedColumns)->get();
        return view('pages.sg.users', ['users' => $usersWithRoles]);
    }

    public function previousReviews($requestedId) {
        $previousReviews = Requisition::where('requisitions.requested_disc_code', $requestedId)
                                    ->select(
                                        'requisitions.id',
                                        'taken_disciplines.code AS taken_codes',
                                        'taken_disciplines.year AS year_taken',
                                        'taken_disciplines.semester AS semester_taken',
                                        'taken_disciplines.institution',
                                        'requisitions.result', 
                                        'requisitions.updated_at AS result_date',
                                        'requisitions.result_text AS result_text'
                                    )
                                ->join('taken_disciplines', 'requisitions.id', '=', 'taken_disciplines.requisition_id') //inner join
                                ->get()
                                ->groupBy('id');
        
        $previousReviewsFiltered = $previousReviews->filter(function ($group){
            return $group->contains(function ($object){
                return $object->institution === request()->institution;
            });
        });

        return view('pages.sg.previousReviews', ['requisitions' => $previousReviewsFiltered]);
    }

    public function reviews($requisitionId) {
        $req = Requisition::with('reviews')->find($requisitionId);
        
        return view('pages.sg.reviews', ['requisitionId' => $requisitionId, 'reviews' => $req->reviews]);
    }

    public function reviewerPick($requisitionId) {
        $reviewRole = Role::where('name', RoleName::REVIEWER)->first();

        $reviewers = $reviewRole->users;

        return view('pages.sg.reviewerPick', ['reviewers' => $reviewers, 'requisitionId' => $requisitionId]);
    }

}
