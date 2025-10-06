<?php

namespace App\Http\Controllers;

use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Log;
use Inertia\Inertia;
use App\Enums\RoleId;
use App\Models\Requisition;
use App\Models\Department;
use App\Enums\EventType;

class ListController extends Controller
{
    public function list()
    {
        $user = Auth::user();
        $roleId = $user->current_role_id;

        Log::debug('ListController::list - Requisition list requested', [
            'user_codpes' => $user->codpes,
            'role_id' => $roleId
        ]);

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

        Log::debug('ListController::list - Requisition list retrieved, loading RequisitionListPage', [
            'user_codpes' => $user->codpes,
            'role_id' => $roleId,
            'requisition_count' => count($requisitions)
        ]);

        return Inertia::render('RequisitionListPage', [
            'label' => 'Requerimentos',
            'requisitions' => $requisitions,
            'selectedColumns' => $selectedColumns,
            'selectedActions' => $selectedActions,
        ]);
    }

    private function studentList($user)
    {
        Log::debug('ListController::studentList - Getting student requisitions', ['user_codpes' => $user->codpes]);

        $selectedColumns = ['id', 'created_at', 'requested_disc', 'situation'];
        $requisitions = Requisition::with('takenDisciplines')->select($selectedColumns)->where('student_nusp', $user->codpes)->get();
        $selectedActions = [['new_requisition']];

        Log::debug('ListController::studentList - Student requisitions retrieved', [
            'user_codpes' => $user->codpes,
            'count' => count($requisitions)
        ]);

        return [$requisitions, $selectedColumns, $selectedActions];
    }

    private function sgList()
    {
        Log::debug('ListController::sgList - Getting SG requisitions');

        $selectedColumns = ['created_at', 'updated_at', 'id', 'student_name', 'student_nusp', 'internal_status', 'department'];
        $requisitions = Requisition::select($selectedColumns)->get();
        $selectedActions = [['admin', 'new_requisition', 'export']];

        Log::debug('ListController::sgList - SG requisitions retrieved', ['count' => count($requisitions)]);

        return [$requisitions, $selectedColumns, $selectedActions];
    }

    private function secretaryList($user)
    {
        Log::debug('ListController::secretaryList - Getting secretary requisitions', [
            'user_codpes' => $user->codpes,
            'department_id' => $user->current_department_id
        ]);

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

        Log::debug('ListController::secretaryList - Secretary requisitions retrieved', [
            'user_codpes' => $user->codpes,
            'department_name' => $departmentName,
            'count' => count($requisitions)
        ]);

        return [$requisitions, $selectedColumns, $selectedActions];
    }

    private function reviewerList($user)
    {
        Log::debug('ListController::reviewerList - Getting reviewer requisitions', ['user_codpes' => $user->codpes]);

        $selectedReviewColumns = ['requisitions.created_at', 'student_name', 'requested_disc', 'reviewer_decision', 'reviews.updated_at', 'requisitions.id'];
        $requisitions = DB::table('reviews')
            ->join('requisitions', 'reviews.requisition_id', '=', 'requisitions.id')
            ->where('reviewer_nusp', $user->codpes)
            ->where('requisitions.situation', '=', 'Enviado para análise dos pareceristas')
            ->select($selectedReviewColumns)->get();

        $selectedColumnsNames = ['created_at', 'student_name', 'requested_disc', 'reviewer_decision', 'updated_at', 'id'];
        $selectedActions = [];

        Log::debug('ListController::reviewerList - Reviewer requisitions retrieved', [
            'user_codpes' => $user->codpes,
            'count' => count($requisitions)
        ]);

        return [$requisitions, $selectedColumnsNames, $selectedActions];
    }
}
