<?php

namespace App\Http\Controllers;

use App\Enums\RoleId;
use App\Models\User;
use App\Models\Department;
use App\Models\Role;
use App\Models\Replicado\ReplicadoUser;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Log;

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
        Log::info('RoleController::addRole - Adding role to user', [
            'target_nusp' => $request->nusp,
            'role_id' => $request->roleId,
            'department_id' => $request->departmentId ?? null,
            'caller_user_codpes' => Auth::user()->codpes
        ]);

        $data = $this->validateRoleRequest($request);

        if ($response = $this->checkUnauthorizedSG($data['roleId'])) {
            Log::warning('RoleController::addRole - Unauthorized SG role assignment attempt', [
                'caller_user_codpes' => Auth::user()->codpes,
                'target_role' => $data['roleId']
            ]);
            return $response;
        }

        try {
            DB::transaction(function () use ($data) {
                $userExists = User::where('codpes', $data['nusp'])->exists();

                $userAttributes = [
                    'codpes' => $data['nusp'],
                    'current_role_id' => $data['roleId']
                ];

                if (!$userExists) {
                    Log::info('RoleController::addRole - User does not exist, creating new user', [
                        'nusp' => $data['nusp']
                    ]);

                    $replicadoUser = ReplicadoUser::where('nusp', $data['nusp'])->first();

                    if ($replicadoUser) {
                        $userAttributes['name'] = $replicadoUser->name;
                        Log::info('RoleController::addRole - Found user in Replicado', [
                            'nusp' => $data['nusp'],
                            'name' => $replicadoUser->name
                        ]);
                    }
                }

                $targetUser = User::firstOrCreate(['codpes' => $data['nusp']], $userAttributes);
                $targetUser->assignRole($data['roleId'], $data['departmentId'] ?? null);

                Log::info('RoleController::addRole - Role assigned successfully', [
                    'user_codpes' => $targetUser->codpes,
                    'role_id' => $data['roleId'],
                    'department_id' => $data['departmentId'] ?? null
                ]);
            });

            Log::info('RoleController::addRole - Role addition completed successfully', [
                'nusp' => $data['nusp'],
                'role_id' => $data['roleId']
            ]);

            return redirect()->back()->with('success', 'Role added successfully.');
        } catch (\Exception $e) {
            Log::error('RoleController::addRole - Failed to add role', [
                'nusp' => $data['nusp'],
                'role_id' => $data['roleId'],
                'error' => $e->getMessage()
            ]);

            return redirect()->back()->with('error', 'Failed to add role.');
        }
    }

    public function removeRole(Request $request)
    {
        Log::info('RoleController::removeRole - Removing role from user', [
            'nusp' => $request->nusp,
            'role_id' => $request->roleId,
            'department_id' => $request->departmentId ?? null,
            'caller_user_codpes' => Auth::user()->codpes
        ]);

        $data = $this->validateRoleRequest($request);

        if ($response = $this->checkUnauthorizedSG($data['roleId'])) {
            Log::warning('RoleController::removeRole - Unauthorized SG role removal attempt', [
                'caller_user_codpes' => Auth::user()->codpes,
                'target_role' => $data['roleId']
            ]);
            return $response;
        }

        try {
            $user = User::where('codpes', $data['nusp'])->first();

            if (!$user) {
                Log::warning('RoleController::removeRole - User not found', [
                    'nusp' => $data['nusp']
                ]);
                return response()->json(['error' => 'User not found'], 404);
            }

            $user->removeRole($data['roleId'], $data['departmentId'] ?? null);

            Log::info('RoleController::removeRole - Role removed successfully', [
                'nusp' => $data['nusp'],
                'role_id' => $data['roleId'],
                'department_id' => $data['departmentId'] ?? null
            ]);

            return response()->json(['success' => true], 200);
        } catch (\Exception $e) {
            Log::error('RoleController::removeRole - Failed to remove role', [
                'nusp' => $data['nusp'],
                'role_id' => $data['roleId'],
                'error' => $e->getMessage()
            ]);

            return response()->json(['error' => 'Failed to remove role'], 500);
        }
    }

    public function switchRole(Request $request)
    {
        Log::info('RoleController::switchRole - Switching user role', [
            'role_id' => $request->roleId,
            'department_id' => $request->departmentId ?? null,
            'user_codpes' => Auth::user()->codpes
        ]);

        $user = Auth::user();
        if (!$user instanceof User) {
            Log::error('RoleController::switchRole - Authenticated user is not a valid User instance', [
                'user_codpes' => Auth::user()->codpes
            ]);
            return response()->json(['error' => 'Unauthorized'], 403);
        }

        if (!$user->hasRole($request['roleId'], $request['departmentId'] ?? null)) {
            Log::warning('RoleController::switchRole - User does not have requested role', [
                'user_codpes' => $user->codpes,
                'requested_role' => $request->roleId,
                'requested_department' => $request->departmentId ?? null
            ]);
            return response()->json(['error' => 'Unauthorized'], 403);
        }

        try {
            $user->changeCurrentRole($request['roleId'], $request['departmentId'] ?? null);

            Log::info('RoleController::switchRole - Role switched successfully', [
                'user_codpes' => $user->codpes,
                'new_role' => $request->roleId,
                'new_department' => $request->departmentId ?? null
            ]);
        } catch (\Exception $e) {
            Log::error('RoleController::switchRole - Failed to switch role', [
                'user_codpes' => $user->codpes,
                'error' => $e->getMessage()
            ]);
            return response()->json(['error' => $e->getMessage()], 403);
        }

        $user->current_role_id = (int) $request['roleId'];
        $user->current_department_id = $request['departmentId'] != null ? (int) $request['departmentId'] : null;
        $user->save();

        return redirect()->back();
    }

    public function listRolesAndDepartments()
    {
        Log::info('RoleController::listRolesAndDepartments - Fetching roles and departments', [
            'caller_user_codpes' => Auth::user()->codpes
        ]);

        $user = Auth::user();
        $rolesQuery = Role::where('id', '!=', RoleId::STUDENT);

        if ($user && $user->current_role_id == RoleId::SECRETARY) {
            $roles = $rolesQuery->where('id', '!=', RoleId::SG)->get();

            Log::info('RoleController::listRolesAndDepartments - Secretary role data retrieved', [
                'user_codpes' => $user->codpes,
                'roles_count' => count($roles)
            ]);

            return response()->json([
                'roles' => $roles,
            ]);
        } else {
            $roles = $rolesQuery->get();
            $departments = Department::all();

            Log::info('RoleController::listRolesAndDepartments - Full role and department data retrieved', [
                'user_codpes' => $user->codpes,
                'roles_count' => count($roles),
                'departments_count' => count($departments)
            ]);

            return response()->json([
                'roles' => $roles,
                'departments' => $departments,
            ]);
        }
    }
}
