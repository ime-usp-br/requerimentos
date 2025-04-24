<?php

namespace App\Http\Controllers;

use App\Enums\RoleId;
use App\Models\User;
use App\Models\RequisitionsPeriod;
use Inertia\Inertia;
use Illuminate\Http\Request;

class AdminController extends Controller
{
	public function admin()
	{
		$selectedColumns = ['name', 'codpes', 'id'];
		$usersWithRoles = User::whereHas('roles', function ($query) {
				$query->where('id', '!=', RoleId::STUDENT);
			})
			->select($selectedColumns)
			->get()
			->map(function ($user) {
				return [
					"codpes" => $user->codpes,
					"name" => $user->name,
					"roles" => $user->roles,
				];
			});

		return Inertia::render('AdminPage', ['users' => $usersWithRoles]);
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
