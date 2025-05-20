<?php

namespace App\Http\Controllers;

use App\Enums\RoleId;
use App\Models\User;
use App\Models\RequisitionsPeriod;
use Inertia\Inertia;
use Illuminate\Http\Request;

class AdminController extends Controller
{
	public function admin(Request $request)
	{
		$currentUser = $request->user();

		$users = User::with(['departmentUserRoles.role', 'departmentUserRoles.department'])
			->get()
			->flatMap(function ($user) use ($currentUser) {
				return $user->departmentUserRoles
					->filter(function ($dur) use ($currentUser) {
						if ($dur->role_id == RoleId::STUDENT) {
							return false;
						}
						if ($currentUser->current_role_id == RoleId::SECRETARY) {
							return $dur->role_id != RoleId::SG
								&& $dur->department_id == $currentUser->current_department_id;
						}
						return true;
					})
					->map(function ($dur) use ($user) {
						return [
							'nusp' => $user->codpes,
							'name' => $user->name,
							'roleId' => $dur->role_id,
							'roleName' => optional($dur->role)->name,
							'departmentId' => $dur->department_id,
							'departmentName' => optional($dur->department)->name,
						];
					});
			})
			->values();

		return Inertia::render('AdminPage', ['systemUsers' => $users]);
	}

	public function getRequisitionPeriodStatus()
	{
		$requisition_period_status = RequisitionsPeriod::latest('id')->first();

		return response()->json([
			'isCreationEnabled' => $requisition_period_status->is_creation_enabled,
			'isUpdateEnabled' => $requisition_period_status->is_update_enabled,
		]);
	}

	public function setRequisitionPeriodStatus(Request $request)
	{
		$request->validate([
			'isCreationEnabled' => 'required|boolean',
			'isUpdateEnabled' => 'required|boolean',
		]);

		$newStatus = new RequisitionsPeriod;
		$newStatus->is_creation_enabled = $request->isCreationEnabled;
		$newStatus->is_update_enabled = $request->isUpdateEnabled;
		$newStatus->save();

		return redirect()->back()->with('success', 'Requisition period status updated successfully.');
	}

}
