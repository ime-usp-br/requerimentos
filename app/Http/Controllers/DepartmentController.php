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

}
