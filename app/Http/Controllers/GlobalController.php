<?php

namespace App\Http\Controllers;

use App\Models\User;
use App\Enums\RoleId;
use App\Enums\RoleName;
use App\Models\Document;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\Storage;
use Laravel\Socialite\Facades\Socialite;
use Illuminate\Support\Collection;


class GlobalController extends Controller
{
    public function callbackHandler() {
        $userSenhaUnica = Socialite::driver('senhaunica')->user();

        
        // Verificando se o usuário é do IME
        $fromIME = false;
        foreach ($userSenhaUnica->vinculo as $vinculo){
            if (isset($vinculo["siglaUnidade"]) && $vinculo["siglaUnidade"] === 'IME') {
                $fromIME = true;
                break; 
            }
        }
        
        if (!$fromIME) {
            redirect('/acesso-negado');
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
    }

    public function documentHandler($documentId) {

        $user = Auth::user();

        $document = Document::with('requisition')->find($documentId);

        if (!$document) {
            abort(404);
        } 

        if ($user->current_role_id !== RoleId::STUDENT) {
            $filePath = Storage::disk('local')->path($document->path);
            return response()->file($filePath, ['Content-Disposition' => 'inline; filename="Documento"']);
        }

        if ($user->codpes !== $document->requisition->nusp) {
            abort(404);
        }

        $filePath = Storage::disk('local')->path($document->path);
        return response()->file($filePath, ['Content-Disposition' => 'inline; filename="Documento"']);
    }
    
}
