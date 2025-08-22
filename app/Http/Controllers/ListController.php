<?php

namespace App\Http\Controllers;

use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\DB;
use Inertia\Inertia;
use App\Enums\RoleId;
use App\Models\Requisition;
use App\Models\Department;
use App\Enums\EventType;

class ListController extends Controller
{
    public function list() {
        $user = Auth::user();
        $roleId = $user->current_role_id;

        switch ($roleId) {
            case RoleId::STUDENT:
                [$requisitions, $selectedColumns, $selectedActions] = $this->studentList($user);
                break;
            case RoleId::SG:
                [$requisitions, $selectedColumns, $selectedActions] = $this->sgList($user);
                break;
            case RoleId::SECRETARY:
                [$requisitions, $selectedColumns, $selectedActions] = $this->secretaryList($user);
                break;
            case RoleId::REVIEWER:
                [$requisitions, $selectedColumns, $selectedActions] = $this->reviewerList($user);
                break;
        }
        
        return Inertia::render('RequisitionListPage', [
            'label' => 'Aproveitamento de Estudos',
            'requisitions' => $requisitions, 
            'selectedColumns' => $selectedColumns,
            'selectedActions' => $selectedActions,
        ]);
    }

    private function studentList($user) { 
        $selectedColumns = ['id', 'created_at', 'requested_disc_code', 'situation'];
        $requisitions = Requisition::with('takenDisciplines')->select($selectedColumns)->where('student_nusp', $user->codpes)->get();
        $selectedActions = [['new_requisition']];
        return [$requisitions, $selectedColumns, $selectedActions];
    }

    private function sgList() { 
        $selectedColumns = ['id', 'student_name', 'student_nusp', 'requested_disc_code', 'department', 'created_at', 'updated_at', 'internal_status'];
        $requisitions = Requisition::select($selectedColumns)->get();
        $selectedActions = [['admin', 'new_requisition', 'export']];
        return [$requisitions, $selectedColumns, $selectedActions];
    }

    private function secretaryList($user) {
        $statuses = [
            EventType::SENT_TO_DEPARTMENT,
            EventType::SENT_TO_REVIEWERS,
            EventType::RETURNED_BY_REVIEWER,
        ];

        $selectedColumns = ['id', 'created_at', 'updated_at', 'requested_disc', 'situation', 'department'];

        $departmentName = Department::where('id', $user->current_department_id)->value('name');

        $requisitions = Requisition::with('takenDisciplines')
            ->select($selectedColumns)
            ->where('department', $departmentName)
            ->whereIn('situation', $statuses)
            ->get();

        $selectedActions = [['admin']];
        return [$requisitions, $selectedColumns, $selectedActions];
    }

    private function reviewerList($user) { 
        $selectedReviewColumns = ['requisitions.created_at', 'student_name', 'requested_disc', 'reviewer_decision', 'reviews.updated_at', 'requisitions.id'];
        $requisitions = DB::table('reviews')
			->join('requisitions', 'reviews.requisition_id', '=', 'requisitions.id')
			->where('reviewer_nusp', $user->codpes)
			->where('requisitions.situation', '=', 'Enviado para análise dos pareceristas')
			->select($selectedReviewColumns)->get();

		$selectedColumnsNames = ['created_at', 'student_name', 'requested_disc', 'reviewer_decision', 'updated_at', 'id'];
        $selectedActions = [];
        return [$requisitions, $selectedColumnsNames, $selectedActions];
    }
}
