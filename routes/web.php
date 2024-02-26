<?php


use App\Http\Controllers\AuxController;
use Illuminate\Support\Facades\Route;
use App\Models\User;
use App\Http\Controllers\StudentController;
use App\Http\Controllers\RoleController;
use App\Http\Controllers\SGController;
use App\Enums\RoleId;
use Spatie\Permission\Models\Role;

Route::get('/', function () {
    return view('pages.home');
});

Route::get('/acesso-negado', function() {
    return view('pages.accessDenied');
});

Route::get('/callback', [AuxController::class, 'callbackHandler']);


Route::middleware('auth')->group(function() {
    
    Route::prefix('aluno')->group(function () {

        Route::get('/lista', [StudentController::class, 'list'])->name('student.list');

        Route::view('/novo-requerimento', 'pages.student.newRequisition')->name('student.newRequisition');

        Route::get('/detalhe/{requisitionId}', [StudentController::class, 'show'])->name('student.show');

        Route::post('/novo-requerimento', [StudentController::class, 'create'])->name('student.create');

        Route::get('/atualizar/{requisitionId}', [StudentController::class, 'show'])->name('student.edit');

        Route::post('/atualizar/{requisitionId}', [StudentController::class, 'update'])->name('student.update');
    });

    Route::prefix('secretaria')->group(function () {

        Route::get('/lista', [SGController::class, 'list'])->name('sg.list');

        Route::view('/novo-requerimento', 'pages.sg.newRequisition')->name('sg.newRequisition');

        Route::get('/detalhe/{requisitionId}', [SGController::class, 'show'])->name('sg.show');

        Route::post('/novo-requerimento', [SGController::class, 'create'])->name('sg.create');

        Route::post('/atualizar/{requisitionId}', [SGController::class, 'update'])->name('sg.update');

        Route::get('/usuarios', [SGController::class, 'users'])->name('sg.users');

        Route::post('/dar-papel', [RoleController::class, 'addRole'])->name('role.add');

        Route::post('/trocar-papel', [RoleController::class, 'switchRole'])->name('role.switch');

        Route::post('/remover-papel', [RoleController::class, 'removeRole'])->name('role.remove');
    });

    Route::prefix('coordenador')->group(function () {
        Route::get('/lista', function() {
            echo "pagina do coordenador";
        })->name('coordinator.list');
    });

    Route::prefix('parecerista')->group(function () {
        Route::get('/lista', function() {
            echo "pagina do parecerista";
        })->name('reviewer.list');
    });
});