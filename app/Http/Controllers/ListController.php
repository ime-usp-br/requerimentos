<?php

namespace App\Http\Controllers;

use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\DB;
use Inertia\Inertia;
use App\Enums\RoleId;
use App\Models\Requisition;
use App\Models\RequisitionsPeriod;

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
        
        $requisition_period_status = RequisitionsPeriod::latest('id')->first()->is_enabled;

        return Inertia::render('RequisitionList', [
            'label' => 'Requerimentos',
            'requisitions' => $requisitions, 
            'selectedColumns' => $selectedColumns,
            'selectedActions' => $selectedActions,
            'roleId' => $roleId, 
            'useActions' => true,
            'userRoles' => $user->roles,
            'requisitionPeriodStatus' => $requisition_period_status
        ]);
    }

    private function studentList($user) { 
        $selectedColumns = ['id', 'created_at', 'requested_disc', 'situation'];
        $requisitions = Requisition::with('takenDisciplines')->select($selectedColumns)->where('student_nusp', $user->codpes)->get();
        $selectedActions = [['new_requisition']];
        return [$requisitions, $selectedColumns, $selectedActions];
    }

    private function sgList($user) { 
        $selectedColumns = ['created_at', 'updated_at', 'id', 'student_name', 'student_nusp', 'internal_status', 'department'];
        $requisitions = Requisition::select($selectedColumns)->get();
        $selectedActions = [['admin', 'new_requisition', 'export']];
        return [$requisitions, $selectedColumns, $selectedActions];
    }

    private function secretaryList($user) { 
        $selectedColumns = ['id', 'created_at', 'requested_disc', 'situation'];
        $requisitions = Requisition::with('takenDisciplines')->select($selectedColumns)->where('student_nusp', $user->codpes)->get();
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
