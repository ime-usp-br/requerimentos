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

class DepartmentController extends Controller
{
    public function list($departmentName) {

        $selectedColumns = ['created_at', 'student_name', 'nusp', 'internal_status', 'department', 'id'];

        $reqs = Requisition::select($selectedColumns)->where('department', strtoupper($departmentName))->get();

        return view('pages.department.list', ['reqs' => $reqs]);
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

        return view('pages.department.detail', ['req' => $req, 'takenDiscs' => $req->takenDisciplines, 'takenDiscsRecords' => $takenDiscsRecords, 'currentCourseRecords' => $currentCourseRecords, 'takenDiscSyllabi' => $takenDiscSyllabi, 'requestedDiscSyllabi' => $requestedDiscSyllabi, 'departmentName' => strtolower($req->department)]);
    }

}
