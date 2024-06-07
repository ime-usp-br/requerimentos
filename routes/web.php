<?php

use Illuminate\Support\Facades\Route;
use App\Http\Controllers\SGController;
use App\Http\Controllers\RoleController;
use App\Http\Controllers\GlobalController;
use App\Http\Controllers\RecordController;
use App\Http\Controllers\ReviewController;
use App\Http\Controllers\StudentController;
use App\Http\Controllers\DepartmentController;

Route::get('/', function () {
    return view('pages.home');
});

// rota usada para achar informações sobre a configuração de php da máquina
Route::get('/phpinfo', function () {
    phpinfo();
});

Route::get('/acesso-negado', function() {
    return view('pages.accessDenied');
});

Route::get('/callback', [GlobalController::class, 'callbackHandler']);

Route::get('/documento/{documentId}', [GlobalController::class, 'documentHandler'])->name('document.show');

Route::post('/trocar-papel', [RoleController::class, 'switchRole'])->name('role.switch');


Route::middleware('auth')->group(function() {
    
    // Route::prefix('aluno')->middleware('role:Aluno')->group(function () {
    Route::prefix('aluno')->group(function () {
        Route::get('/lista', [StudentController::class, 'list'])->name('student.list');

        Route::view('/novo-requerimento', 'pages.student.newRequisition')->name('student.newRequisition');

        Route::get('/detalhe/{requisitionId}', [StudentController::class, 'show'])->name('student.show');

        Route::post('/novo-requerimento', [StudentController::class, 'create'])->name('student.create');

        Route::get('/atualizar/{requisitionId}', [StudentController::class, 'show'])->name('student.edit');

        Route::post('/atualizar/{requisitionId}', [StudentController::class, 'update'])->name('student.update');
    });

    // Route::prefix('secretaria')->middleware('role:Secretaria de Graduação')->group(function () {
    Route::prefix('secretaria')->group(function () {

        Route::get('/lista', [SGController::class, 'list'])->name('sg.list');

        Route::view('/novo-requerimento', 'pages.sg.newRequisition')->name('sg.newRequisition');

        Route::get('/detalhe/{requisitionId}', [SGController::class, 'show'])->name('sg.show');
        
        Route::get('/historico-disciplina/{subjectID}', [SGController::class, 'discHistory'])->name('sg.discHistory');

        Route::post('/novo-requerimento', [SGController::class, 'create'])->name('sg.create');

        Route::post('/atualizar/{requisitionId}', [SGController::class, 'update'])->name('sg.update');

        Route::get('/usuarios', [SGController::class, 'users'])->name('sg.users');
        
        Route::post('/enviar-requerimento/{requisitionId}', [ReviewController::class, 'createReview'])->name('sg.sendToReviewer');
    });

    Route::prefix('departamento')->group(function () {
        Route::get('/{departmentName}/lista', [DepartmentController::class, 'list'])->name('department.list');

        Route::get('/{departmentName}/detalhe/{requisitionId}', [DepartmentController::class, 'show'])->name('department.show');

        Route::get('/{departmentName}/usuarios', [DepartmentController::class, 'users'])->name('department.users');
    });

    Route::prefix('parecerista')->group(function () {
        Route::get('/lista', [ReviewController::class, 'list'])->name('reviewer.list');

        Route::get('/detalhe/{requisitionId}', [ReviewController::class, 'show'])->name('reviewer.show');
        
        Route::post('/atualizar/{requisitionId}', [ReviewController::class, 'update'])->name('reviewer.update');
    });

    // Route::group(['middleware' => 'role:Secretaria de Graduação,Secretaria do MAC,Secretaria do MAT,Secretaria do MAE,Secretaria do MAP,Parecerista'], function () {
        
        Route::post('/dar-papel', [RoleController::class, 'addRole'])->name('role.add');

        Route::post('/remover-papel', [RoleController::class, 'removeRole'])->name('role.remove');

        Route::get('/escolher-parecerista/{requisitionId}', [ReviewController::class, 'reviewerPick'])->name('reviewer.reviewerPick');
        
        Route::get('/pareceres/{requisitionId}', [ReviewController::class, 'reviews'])->name('reviewer.reviews');

        Route::get('/historico/requerimento/{requisitionId}', [RecordController::class, 'requisitionRecord'])->name('record.requisition');

        Route::get('/historico/versao/{eventId}', [RecordController::class, 'requisitionVersion'])->name('record.requisitionVersion');

    // });
});