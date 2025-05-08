<?php

namespace App\Http\Controllers;

use App\Enums\RoleId;
use App\Models\User;
use App\Models\Department;
use App\Models\Role;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;

class RoleController extends Controller
{
    private function validateRoleRequest(Request $request, $additionalRules = []) {
        $baseRules = [
            'nusp' => 'required|numeric|integer',
            'roleId' => 'required|exists:roles,id',
            'departmentId' => 'nullable|required_if:roleId,' . RoleId::SECRETARY . ',' . RoleId::REVIEWER . '|exists:departments,id',
        ];

        return $request->validate(array_merge($baseRules, $additionalRules));
    }

    private function checkUnauthorizedSG($roleId) {
        if ($roleId === RoleId::SG && !(Auth::user()->current_role_id === RoleId::SG)) {
            return response()->json(['error' => 'Unauthorized'], 403);
        }

        return null;
    }

    public function addRole(Request $request) {
        $data = $this->validateRoleRequest($request);

        if ($response = $this->checkUnauthorizedSG($data['roleId'])) {
            return $response;
        }

        $targetUser = User::firstOrCreate(['codpes' => $data['nusp']], ['codpes' => $data['nusp'], 'current_roleId' => 1]);
        $targetUser->assignRole($data['roleId'], $data['departmentId'] ?? null);
        return response()->json(['success' => true], 200);
    }

    public function removeRole(Request $request) {
        $data = $this->validateRoleRequest($request);

        if ($response = $this->checkUnauthorizedSG($data['roleId'])) {
            return $response;
        }

        $user = User::where('codpes', $data['nusp'])->first();
        $user->removeRole($data['roleId'], $data['departmentId'] ?? null);
        
        return response()->json(['success' => true], 200);
    }

    public function switchRole(Request $request) {
        $data = $this->validateRoleRequest($request, [
            'nusp' => 'nullable'
        ]);

        $user = Auth::user();

        if (!$user->hasRole($data['roleId'], $data['departmentId'] ?? null)) {
            return response()->json(['error' => 'Unauthorized'], 403);
        }

        try {
            $user->changeCurrentRole($data['roleId'], $data['departmentId'] ?? null);
        } catch (\Exception $e) {
            return response()->json(['error' => $e->getMessage()], 403);
        }

        return redirect()->back();
    }

    public function listRolesAndDepartments()
	{
		$roles = Role::where('id', '!=', RoleId::STUDENT)->get();
		$departments = Department::all();

		dd($roles, $departments);

		return response()->json([
			'roles' => $roles,
			'departments' => $departments,
		]);
	}

}
