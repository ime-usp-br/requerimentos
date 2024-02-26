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

        $user = User::where('codpes', $data['nusp'])->first();
        
        if ($data['role'] === 'Coordenador') {
            $department = $data['department'];

            if ($department === 'MAC') {
                $user->assignRole(RoleName::MAC_COORD);
            } elseif($department === 'MAT') {
                $user->assignRole(RoleName::MAT_COORD);
            } elseif($department === 'MAE') {
                $user->assignRole(RoleName::MAE_COORD);
            } elseif($department === 'MAP') {
                $user->assignRole(RoleName::MAP_COORD);
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
        // dd($request->roleSwitch);
        // if (Auth::user()->hasRole($request->roleSwitch)) {
        $user = Auth::user();
        $user->current_role_id = (int) $request->roleSwitch;
        $user->save();
        
        $rolesRedirects = [[RoleId::REVIEWER, 'reviewer.list'],
                            [RoleId::SG, 'sg.list'],
                            [RoleId::MAC_COORD, 'coordinator.list'],
                            [RoleId::MAT_COORD, 'coordinator.list'],
                            [RoleId::MAE_COORD, 'coordinator.list'],
                            [RoleId::MAP_COORD, 'coordinator.list']];
        
        foreach ($rolesRedirects as $roleRedirect) {
            if ($user->current_role_id === $roleRedirect[0]) {
                return redirect()->route($roleRedirect[1]);
            }
        }
        // dd($request->roleSwitch);
    }
}
