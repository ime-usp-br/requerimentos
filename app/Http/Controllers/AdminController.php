<?php

namespace App\Http\Controllers;

use App\Enums\RoleId;
use App\Models\User;
use App\Models\RequisitionsPeriod;
use Inertia\Inertia;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Log;

class AdminController extends Controller
{
	public function admin(Request $request)
	{
		Log::info('AdminController::admin - Admin page requested', [
			'user_codpes' => $request->user()->codpes,
			'current_role' => $request->user()->current_role_id
		]);

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

		Log::info('AdminController::admin - Users retrieved successfully, loading admin page', [
			'user_count' => count($users),
			'current_user' => $currentUser->codpes
		]);

		return Inertia::render('AdminPage', ['systemUsers' => $users]);
	}

	public function getRequisitionPeriodStatus()
	{
		Log::info('AdminController::getRequisitionPeriodStatus - Fetching requisition period status');

		$requisition_period_status = RequisitionsPeriod::latest('id')->first();

		Log::info('AdminController::getRequisitionPeriodStatus - Status retrieved', [
			'is_creation_enabled' => $requisition_period_status->is_creation_enabled,
			'is_update_enabled' => $requisition_period_status->is_update_enabled
		]);

		return response()->json([
			'isCreationEnabled' => $requisition_period_status->is_creation_enabled,
			'isUpdateEnabled' => $requisition_period_status->is_update_enabled,
		]);
	}

	public function setRequisitionPeriodStatus(Request $request)
	{
		Log::info('AdminController::setRequisitionPeriodStatus - Setting requisition period status', [
			'user_codpes' => $request->user()->codpes,
			'requested_creation_enabled' => $request->isCreationEnabled,
			'requested_update_enabled' => $request->isUpdateEnabled
		]);

		$request->validate([
			'isCreationEnabled' => 'required|boolean',
			'isUpdateEnabled' => 'required|boolean',
		]);

		try {
			$newStatus = new RequisitionsPeriod;
			$newStatus->is_creation_enabled = $request->isCreationEnabled;
			$newStatus->is_update_enabled = $request->isUpdateEnabled;
			$newStatus->save();

			Log::info('AdminController::setRequisitionPeriodStatus - Status updated successfully', [
				'new_status_id' => $newStatus->id,
				'is_creation_enabled' => $newStatus->is_creation_enabled,
				'is_update_enabled' => $newStatus->is_update_enabled
			]);

			return redirect()->back()->with('success', 'Requisition period status updated successfully.');
		} catch (\Exception $e) {
			Log::error('AdminController::setRequisitionPeriodStatus - Failed to update status', [
				'error' => $e->getMessage(),
				'user_codpes' => $request->user()->codpes
			]);

			return redirect()->back()->with('error', 'Failed to update requisition period status.');
		}
	}
}
