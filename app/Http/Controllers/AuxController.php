<?php

namespace App\Http\Controllers;

use Illuminate\Http\Request;
use Laravel\Socialite\Facades\Socialite;
use App\Models\User;
use App\Enums\RoleName;
use App\Enums\RoleId;

class AuxController extends Controller
{
    public function callbackHandler() {
        $userSenhaUnica = Socialite::driver('senhaunica')->user();

        // dd($userSenhaUnica);
        if ($userSenhaUnica->vinculo[0]["siglaUnidade"] != "IME") {
            return redirect('/acesso-negado');
        }

        // se onlyLocalUsers = true, não vamos permitir usuários não cadastrados de logar
        if (config('senhaunica.onlyLocalUsers')) {
            $user = User::newLocalUser($userSenhaUnica->codpes);
            if (!$user) {
                session()->invalidate();
                session()->regenerateToken();
                return redirect('/login?msg=noLocalUser');
            }
        } else {
            $user = User::firstOrNew(['codpes' => $userSenhaUnica->codpes]);
        }

        if ($user->codpes === 10758748) {
            $user->givePermissionTo('admin');
        }

        // bind dos dados retornados
        $user->codpes = $userSenhaUnica->codpes;
        $user->email = $userSenhaUnica->email ?? $userSenhaUnica->emailUsp ?? $userSenhaUnica->emailAlternativo ?? 'invalido' . $user->codpes . '@usp.br';
        $user->name = $userSenhaUnica->nompes;

        $rolesInfo = [[RoleName::REVIEWER, RoleId::REVIEWER, 'sg.list'],
                      [RoleName::SG, RoleId::SG, 'sg.list'],
                      [RoleName::MAC_COORD, RoleId::MAC_COORD, 'coordinator.list'],
                      [RoleName::MAT_COORD, RoleId::MAT_COORD, 'coordinator.list'],
                      [RoleName::MAE_COORD, RoleId::MAE_COORD, 'coordinator.list'],
                      [RoleName::MAP_COORD, RoleId::MAP_COORD, 'coordinator.list']];

        foreach ($rolesInfo as $roleInfo) {
            if ($user->hasRole($roleInfo[0])) {
                $user->current_role_id = $roleInfo[1];
                $user->save();
                \Auth::login($user, true);
                return redirect()->route($roleInfo[2]);
            }
        }
        $user->current_role_id = RoleId::STUDENT;
        $user->save();
        \Auth::login($user, true);
        return redirect()->route('student.list');

        // if ($user->hasRole(RoleName::REVIEWER)) {
        //     $user->current_role_id = RoleId::REVIEWER;
            // $homePage = 'reviewer.list';
        //     $homePage = 'sg.list';
        // } elseif ($user->hasRole(RoleName::SG)) {
        //     $user->current_role_id = 2;
        //     $homePage = 'sg.list';
        // } elseif ($user->hasRole(RoleName::MAC_COORD)) {
        //     $user->current_role_id = RoleId::MAC_COORD;
        //     $homePage = 'coordinator.list';
        // } elseif ($user->hasRole(RoleName::MAT_COORD)) {
        //     $user->current_role_id = RoleId::MAT_COORD;
        //     $homePage = 'coordinator.list';
        // } elseif ($user->hasRole(RoleName::MAE_COORD)) {
        //     $user->current_role_id = RoleId::MAE_COORD;
        //     $homePage = 'coordinator.list';
        // } elseif ($user->hasRole(RoleName::MAP_COORD)) {
        //     $user->current_role_id = RoleId::MAP_COORD;
        //     $homePage = 'coordinator.list';
        // }  
        // } else {
        //     $user->current_role_id = 1;
        //     $homePage = 'student.list';
        // }
        
        // $user->save();

        // $user->setDefaultPermission();
        // \Auth::login($user, true);

        // return redirect()->route($homePage);
    }
}
