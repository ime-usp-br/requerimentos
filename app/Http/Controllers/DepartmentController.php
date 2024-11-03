<?php

namespace App\Http\Controllers;

use App\Enums\DocumentType;
use App\Models\User;
use App\Models\Requisition;
use App\Models\Event;
use App\Enums\EventType;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Auth;

class DepartmentController extends Controller
{
    public function list($departmentName) {

        $selectedColumns = ['created_at', 'student_name', 'student_nusp', 'internal_status', 'id'];

        $reqs = Requisition::select($selectedColumns)->where('department', strtoupper($departmentName))->where('registered', 'Não')->where('validated', true)->get();

        return view('pages.department.list', ['reqs' => $reqs, 'departmentName' => $departmentName]);
    }

    public function show($departmentName, $requisitionId) {
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

        return view('pages.department.detail', ['req' => $req, 'takenDiscs' => $req->takenDisciplines, 'takenDiscsRecords' => $takenDiscsRecords, 'currentCourseRecords' => $currentCourseRecords, 'takenDiscSyllabi' => $takenDiscSyllabi, 'requestedDiscSyllabi' => $requestedDiscSyllabi, 'departmentName' => $departmentName]);
    }

    public function users($departmentName) {
        $selectedColumns = ['name', 'codpes', 'id'];

        $usersWithRoles = User::whereHas('roles')->select($selectedColumns)->get();

        return view('pages.department.users', ['users' => $usersWithRoles, 'departmentName' => $departmentName]);
    }

    public function registered($requisitionId) {
        DB::transaction(function () use ($requisitionId) {

            $user = Auth::user();

            $req = Requisition::find($requisitionId);
            $req->situation = EventType::REGISTERED;
            $req->registered = 'Sim';

            $event = new Event;
            $event->type = EventType::REGISTERED;
            // dd($event);
            $event->requisition_id = $requisitionId;
            $event->author_name = $user->name;
            $event->author_nusp = $user->codpes;
            $event->version = $req->latest_version;

            $event->message = "Registrado por " . $user->name;
            $req->internal_status = "Registrado por " . $user->name;

            $event->save();
            $req->save();
        });

        return response()->noContent();
    }

}
