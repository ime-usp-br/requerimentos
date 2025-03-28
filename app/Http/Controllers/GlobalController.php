<?php

namespace App\Http\Controllers;

use App\Models\User;
use App\Enums\RoleId;
use App\Enums\RoleName;
use Laravel\Socialite\Facades\Socialite;
use Illuminate\Support\Facades\Auth;

class GlobalController extends Controller
{
    // public function callbackHandler() {
    //     $userSenhaUnica = Socialite::driver('senhaunica')->user();

    //     // se onlyLocalUsers = true, não vamos permitir usuários não cadastrados de logar
    //     if (config('senhaunica.onlyLocalUsers')) {
    //         $user = User::newLocalUser($userSenhaUnica->codpes);
    //         if (!$user) {
    //             session()->invalidate();
    //             session()->regenerateToken();
    //             return redirect('/login?msg=noLocalUser');
    //         }
    //     } else {
    //         $user = User::firstOrNew(['codpes' => $userSenhaUnica->codpes]);
    //     }

        
    //     $user->codpes = $userSenhaUnica->codpes;
    //     $user->email = $userSenhaUnica->email ?? $userSenhaUnica->emailUsp ?? $userSenhaUnica->emailAlternativo ?? 'invalido' . $user->codpes . '@usp.br';
    //     $user->name = $userSenhaUnica->nompes;

    //     // a ordem em que os elementos estão nesse vetor determina a prioridade
    //     // que um papel tem quando um usuário com mais de um papel loga no 
    //     // sistema 
    //     $rolesInfo = [[RoleName::REVIEWER, RoleId::REVIEWER, 'reviewer.list'],
    //                 //   [RoleName::MAP_SECRETARY, RoleId::MAP_SECRETARY, 'department.list', 'map'],
    //                 //   [RoleName::MAC_SECRETARY, RoleId::MAC_SECRETARY, 'department.list', 'mac'],
    //                 //   [RoleName::MAT_SECRETARY, RoleId::MAT_SECRETARY, 'department.list', 'mat'],
    //                 //   [RoleName::MAE_SECRETARY, RoleId::MAE_SECRETARY, 'department.list', 'mae'],
    //                 //   [RoleName::VRT_SECRETARY, RoleId::VRT_SECRETARY, 'department.list', 'virtual'],
    //                   [RoleName::SG, RoleId::SG, 'sg.list']];

    //     foreach ($rolesInfo as $roleInfo) {
    //         if ($user->hasRole($roleInfo[0])) {
    //             $user->current_role_id = $roleInfo[1];
    //             $user->save();
    //             Auth::login($user, true);
    //             return redirect()->route($roleInfo[2], ['departmentName' => $roleInfo[3] ?? NULL]);
    //         }
    //     }

    //     // o papel estudante é o default do sistema 
    //     $user->current_role_id = RoleId::STUDENT;
    //     $user->save();
    //     Auth::login($user, true);
    //     return redirect()->route('list');
    // }

    // public function documentHandler($documentId) {

    //     $user = Auth::user();

    //     $document = Document::with('requisition')->find($documentId);

    //     if (!$document) {
    //         abort(404);
    //     } 

    //     if ($user->current_role_id !== RoleId::STUDENT) {
    //         $filePath = Storage::disk('local')->path($document->path);
    //         return response()->file($filePath, ['Content-Disposition' => 'inline; filename="Documento"']);
    //     }

    //     if ($user->codpes !== $document->requisition->student_nusp) {
    //         abort(404);
    //     }

    //     $filePath = Storage::disk('local')->path($document->path);
    //     return response()->file($filePath, ['Content-Disposition' => 'inline; filename="Documento"']);
    // }
    
}
