<?php

namespace App\Http\Controllers;

use App\Enums\DocumentType;
use App\Models\User;
use App\Models\Requisition;

class DepartmentController extends Controller
{
    public function list($departmentName) {

        $selectedColumns = ['created_at', 'student_name', 'student_nusp', 'internal_status', 'id'];

        $reqs = Requisition::select($selectedColumns)->where('department', strtoupper($departmentName))->where('validated', true)->get();

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

}
