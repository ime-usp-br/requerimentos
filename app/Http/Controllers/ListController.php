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
                [$requisitions, $selectedColumns] = $this->studentList($user);
                break;
            case RoleId::SG:
                [$requisitions, $selectedColumns] = $this->sgList($user);
                break;
            case RoleId::SECRETARY:
                [$requisitions, $selectedColumns] = $this->secretaryList($user);
                break;
            case RoleId::REVIEWER:
                [$requisitions, $selectedColumns] = $this->reviewerList($user);
                break;
        }
        
        $requisition_period_status = RequisitionsPeriod::latest('id')->first()->is_enabled;

        return Inertia::render('RequisitionList', [
            'requisitions' => $requisitions, 
            'selectedColumns' => $selectedColumns,
            'roleId' => $roleId, 
            'userRoles' => $user->roles,
            'requisitionPeriodStatus' => $requisition_period_status
        ]);
    }

    private function studentList($user) { 
        $selectedColumns = ['id', 'created_at', 'requested_disc', 'situation'];
        $requisitions = Requisition::with('takenDisciplines')->select($selectedColumns)->where('student_nusp', $user->codpes)->get();
        return [$requisitions, $selectedColumns];
    }

    private function sgList($user) { 
        $selectedColumns = ['created_at', 'id', 'student_name', 'student_nusp', 'internal_status', 'department'];
        $requisitions = Requisition::select($selectedColumns)->get();
        return [$requisitions, $selectedColumns];
    }

    private function secretaryList($user) { 
        $selectedColumns = ['id', 'created_at', 'requested_disc', 'situation'];
        $requisitions = Requisition::with('takenDisciplines')->select($selectedColumns)->where('student_nusp', $user->codpes)->get();
        return [$requisitions, $selectedColumns];
    }

    private function reviewerList($user) { 
        $selectedReviewColumns = ['requisitions.created_at', 'student_name', 'requested_disc', 'reviewer_decision', 'reviews.updated_at', 'requisitions.id'];
        $requisitions = DB::table('reviews')
			->join('requisitions', 'reviews.requisition_id', '=', 'requisitions.id')
			->where('reviewer_nusp', $user->codpes)
			->where('requisitions.situation', '=', 'Enviado para análise dos pareceristas')
			->select($selectedReviewColumns)->get();

		$selectedColumnsNames = ['created_at', 'student_name', 'requested_disc', 'reviewer_decision', 'updated_at', 'id'];
        return [$requisitions, $selectedColumnsNames];
    }
}
