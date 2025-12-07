<?php

namespace App\Http\Controllers;

use App\Enums\RoleId;
use App\Models\User;
use App\Models\Department;
use App\Models\Role;
use App\Models\Replicado\ReplicadoUser;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;
use \Illuminate\Support\Facades\DB;

class RoleController extends Controller
{
    private function validateRoleRequest(Request $request, $additionalRules = [])
    {
        $baseRules = [
            'nusp' => 'required|numeric|integer',
            'roleId' => 'required|exists:roles,id',
            'departmentId' => 'nullable|required_if:roleId,' . RoleId::SECRETARY . ',' . RoleId::REVIEWER . '|exists:departments,id',
        ];

        return $request->validate(array_merge($baseRules, $additionalRules));
    }

    private function checkUnauthorizedSG($roleId)
    {
        if ($roleId === RoleId::SG && !(Auth::user()->current_role_id === RoleId::SG)) {
            return response()->json(['error' => 'Unauthorized'], 403);
        }

        return null;
    }

    public function addRole(Request $request)
    {
        $data = $this->validateRoleRequest($request);

        if ($response = $this->checkUnauthorizedSG($data['roleId'])) {
            return $response;
        }

        DB::transaction(function () use ($data) {
            $userExists = User::where('codpes', $data['nusp'])->exists();

            $userAttributes = [
                'codpes' => $data['nusp'],
                'current_role_id' => $data['roleId']
            ];

            if (!$userExists) {
                $replicadoUser = ReplicadoUser::where('nusp', $data['nusp'])->first();

                if ($replicadoUser) {
                    $userAttributes['name'] = $replicadoUser->name;
                }
            }
            $targetUser = User::firstOrCreate(['codpes' => $data['nusp']], $userAttributes);

            $targetUser->assignRole($data['roleId'], $data['departmentId'] ?? null);
        });

        return redirect()->back()->with('success', 'Role added successfully.');
    }

    public function removeRole(Request $request)
    {
        $data = $this->validateRoleRequest($request);

        if ($response = $this->checkUnauthorizedSG($data['roleId'])) {
            return $response;
        }

        $user = User::where('codpes', $data['nusp'])->first();
        $user->removeRole($data['roleId'], $data['departmentId'] ?? null);

        return response()->json(['success' => true], 200);
    }

    public function switchRole(Request $request)
    {
        $data = $this->validateRoleRequest($request, [
            'nusp' => 'nullable'
        ]);

        $user = Auth::user();

        if (!$user->hasRole($request['roleId'], $request['departmentId'] ?? null)) {
            return response()->json(['error' => 'Unauthorized'], 403);
        }

        try {
            $user->changeCurrentRole($request['roleId'], $request['departmentId'] ?? null);
        } catch (\Exception $e) {
            return response()->json(['error' => $e->getMessage()], 403);
        }

        $user = Auth::user();
        $user->current_role_id = (int) $request['roleId'];
        $user->current_department_id = $request['departmentId'] != null ? (int) $request['departmentId'] : null;
        $user->save();


        return redirect()->back();
    }

    public function listRolesAndDepartments()
    {
        $user = Auth::user();
        $rolesQuery = Role::where('id', '!=', RoleId::STUDENT);

        if ($user && $user->current_role_id == RoleId::SECRETARY) {
            $roles = $rolesQuery->where('id', '!=', RoleId::SG)->get();
            return response()->json([
                'roles' => $roles,
            ]);
        } else {
            $roles = $rolesQuery->get();
            $departments = Department::all();

            return response()->json([
                'roles' => $roles,
                'departments' => $departments,
            ]);
        }
    }
}
