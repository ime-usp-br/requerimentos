<?php

namespace App\Http\Controllers;

use App\Enums\RoleName;
use App\Enums\RoleId;
use Illuminate\Http\Request;
use App\Models\User;
use Illuminate\Support\Facades\Auth;
use Spatie\Permission\Models\Role;

class RoleController extends Controller
{
    public function addRole(Request $request) {
        $inputArray = [
            'nusp' => 'required | numeric | integer',
            'role' => 'required',
            'department' => 'required'
        ];

        $data = $request->validate($inputArray);

        $user = User::firstOrCreate(['codpes' => $data['nusp']], ['codpes' => $data['nusp'], 'current_role_id' => 1]);
        
        if ($data['role'] === 'Coordenador') {
            $department = $data['department'];
            
            if ($department === 'MAC') {
                $user->assignRole(RoleName::MAC_SECRETARY);
            } elseif($department === 'MAT') {
                $user->assignRole(RoleName::MAT_SECRETARY);
            } elseif($department === 'MAE') {
                $user->assignRole(RoleName::MAE_SECRETARY);
            } elseif($department === 'MAP') {
                $user->assignRole(RoleName::MAP_SECRETARY);
            }
        } else {
            $user->assignRole($data['role']);
        }

        return redirect()->route('sg.users');
    }

    public function removeRole(Request $request) {
        $nusp = request('nusp');
        $role = request('role');
        $user = User::where('codpes', $nusp)->first();
        $user->removeRole($role);

        return response()->noContent();
    }

    public function switchRole(Request $request) {
        $user = Auth::user();
        $user->current_role_id = (int) $request->roleSwitch;
        $user->save();
        
        $rolesRedirects = [[RoleId::REVIEWER, 'reviewer.list'],
                           [RoleId::SG, 'sg.list'],
                           [RoleId::MAC_SECRETARY, 'department.list', 'mac'],
                           [RoleId::MAT_SECRETARY, 'department.list', 'mat'],
                           [RoleId::MAE_SECRETARY, 'department.list', 'mae'],
                           [RoleId::MAP_SECRETARY, 'department.list', 'map']];
        
        foreach ($rolesRedirects as $roleRedirect) {
            if ($user->current_role_id === $roleRedirect[0]) {
                return redirect()->route($roleRedirect[1], ['departmentName' => $roleRedirect[2] ?? NULL]);
            }
        }
    }
}
