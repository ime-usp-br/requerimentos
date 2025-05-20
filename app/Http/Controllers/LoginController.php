<?php

namespace App\Http\Controllers;

use App\Http\Controllers\Controller;
use App\Enums\RoleId;
use App\Models\User;
use Laravel\Socialite\Facades\Socialite;
use Illuminate\Support\Facades\Auth;

class LoginController extends Controller
{
	public function redirectToProvider()
	{
		return Socialite::driver('senhaunica')->redirect();
	}

	public function callbackHandler()
	{
		$userSenhaUnica = Socialite::driver('senhaunica')->user();

		$fromIME = false;
        foreach ($userSenhaUnica->vinculo as $vinculo){
            if (isset($vinculo["siglaUnidade"]) && $vinculo["siglaUnidade"] === 'IME') {
                $fromIME = true;
                break; 
            }
        }
        
        if (!$fromIME) {
            abort(403, 'Acesso negado. Seu vínculo com o IME não foi encontrado. Se você for aluno do IME, entre em contato com o serviço de graduação.');
        }

		$user = User::where('codpes', $userSenhaUnica->codpes)->first();
		if (is_null($user)) {
			$user = new User;
			$user->codpes = $userSenhaUnica->codpes;
			$user->email = $userSenhaUnica->email;
			$user->name = $userSenhaUnica->nompes;
			$user->current_role_id = RoleId::STUDENT;
			$user->current_department_id = null;
			$user->save();
			$user->assignRole(RoleId::STUDENT);
		}
		Auth::login($user, true);
		return redirect()->route('list');
	}


	public function logout()
	{
		Auth::logout();
		return redirect('/');
	}
}
