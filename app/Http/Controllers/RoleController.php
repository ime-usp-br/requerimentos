<?php

namespace App\Http\Controllers;

use App\Enums\RoleId;
use App\Models\User;
use App\Models\Department;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;

class RoleController extends Controller
{
    public function addRole(Request $request) {
        $validationRules = [
            'nusp' => 'required | numeric | integer',
            'role' => 'required',
            'department' => 'required_if:role,department'
        ];        
        $data = $request->validate($validationRules);
        
        if ($data['role'] === 'Serviço de Graduação' && !(Auth::user()->current_role_id === RoleId::SG)) {
            return response()->json(['error' => 'Unauthorized'], 403);
        }
        
        $targetUser = User::firstOrCreate(['codpes' => $data['nusp']], ['codpes' => $data['nusp'], 'current_role_id' => 1]);
        $targetUser->assignRole($data['role']);
        
        if ($data['role'] === 'Secretaria') {
            $department = Department::where('code', $data['department'])->first();
            if ($department) {
                $targetUser->departments()->syncWithoutDetaching([$department->id]);
            }
        }
        return back();
    }

    public function removeRole(Request $request) {
        $validationRules = [
            'nusp' => 'required | numeric | integer',
            'role' => 'required',
            'department' => 'required_if:role,department'
        ];        
        $data = $request->validate($validationRules);

        if ($data['role'] === 'Serviço de Graduação' && !(Auth::user()->current_role_id === RoleId::SG)) {
            return response()->json(['error' => 'Unauthorized'], 403);
        }
        
        $user = User::where('codpes', $data['nusp'])->first();
        $user->removeRole($data['role']);
        
        if ($data['role'] === 'Secretaria') {
            $departmentCode = $data['department'];
            $department = Department::where('code', $departmentCode)->first();
            if ($department) {
                $user->departments()->detach($department->id);
            }
        }


        return response()->json(['success' => true], 200);
    }

    public function switchRole(Request $request) {
        $validationRules = [
            'role-switch' => 'required|exists:roles,id'
        ];        
        $data = $request->validate($validationRules);

        $user = Auth::user();
        $newRoleId = (int) $data['role-switch'];

        if (!$user->roles->contains('id', $newRoleId)) {
            return response()->json(['error' => 'Unauthorized'], 403);
        }

        $user->current_role_id = $newRoleId;
        $user->save();
        
        return redirect()->back();
    }
}