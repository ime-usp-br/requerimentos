<?php

namespace App\Http\Controllers;

use App\Http\Controllers\Controller;
use App\Enums\RoleId;
use App\Models\User;
use Laravel\Socialite\Facades\Socialite;
use Illuminate\Support\Facades\Auth;
use \Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Log;

class LoginController extends Controller
{
	public function redirectToProvider()
	{
		return Socialite::driver('senhaunica')->redirect();
	}

	public function callbackHandler()
	{
		$userSenhaUnica = Socialite::driver('senhaunica')->user();

		Log::info('LoginController::callbackHandler - User login attempt: ', 
			['user_data' => json_encode($userSenhaUnica->user ?? $userSenhaUnica, JSON_PRETTY_PRINT)]);

		// Nós tentamos garantir que o usuário tenha vínculo com o IME, mas 
		// na prática o objeto de login de alguns usuários vem diferente. 
		// Tivemos problemas com alunos que recém-ingressaram e alunos de transferência.
		//
		// $fromIME = false;
		// foreach ($userSenhaUnica->vinculo as $vinculo){
		//     if (isset($vinculo["siglaUnidade"]) && $vinculo["siglaUnidade"] === 'IME') {
		//         $fromIME = true;
		//         break; 
		//     }
		// }

		// if (!$fromIME) {
		//     abort(403, 'Acesso negado. Seu vínculo com o IME não foi encontrado. Se você for aluno do IME, entre em contato com o serviço de graduação.');
		// }

		$user = User::where('codpes', $userSenhaUnica->codpes)->first();
		if (is_null($user)) {
			Log::info('LoginController::callbackHandler - User not found, creating new user', ['codpes' => $userSenhaUnica->codpes]);

			$user = DB::transaction(function () use ($userSenhaUnica) {
				$user = new User;
				$user->codpes = $userSenhaUnica->codpes;
				$user->email = $userSenhaUnica->emailUsp ?? $userSenhaUnica->email
					?? $userSenhaUnica->emailAlternativo
					?? 'invalido' . $userSenhaUnica->codpes . '@usp.br';
				$user->name = $userSenhaUnica->nompes;
				$user->current_role_id = RoleId::STUDENT;
				$user->current_department_id = null;
				$user->save();
				$user->assignRole(RoleId::STUDENT);
				return $user;
			});
		}
		else if (is_null($user->email) || is_null($user->name)) {
			// O usuário pode ter sido cadastrado por meio de uma atribuição de role.
			// Nesse caso, o email dele estará nulo, e teremos que completar as informações.
			// Se o replicado não tiver encontrado o usuário, é possível que o nome esteja nulo também
			Log::info('LoginController::callbackHandler - User with incomplete data, updating', ['codpes' => $userSenhaUnica->codpes]);

			DB::transaction(function () use ($user, $userSenhaUnica) {
				$user->email = $user->email ?? $userSenhaUnica->emailUsp
					?? $userSenhaUnica->email
					?? $userSenhaUnica->emailAlternativo
					?? 'invalido' . $user->codpes . '@usp.br';
				$user->name = $user->name ?? $userSenhaUnica->nompes;

				$user->save();
				$user->assignRole(RoleId::STUDENT);
			});
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
