<?php

use Illuminate\Support\Facades\Route;
use App\Http\Controllers\SGController;
use App\Http\Controllers\RoleController;
use App\Http\Controllers\GlobalController;
use App\Http\Controllers\ReviewController;
use App\Http\Controllers\StudentController;
use App\Http\Controllers\DepartmentController;

Route::get('/', function () {
    return view('pages.home');
});

Route::get('/phpinfo', function () {
    phpinfo();
});

Route::get('/acesso-negado', function() {
    return view('pages.accessDenied');
});

Route::get('/callback', [GlobalController::class, 'callbackHandler']);

Route::get('/documento/{documentId}', [GlobalController::class, 'documentHandler'])->name('document.show');

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

        Route::post('/novo-requerimento', [SGController::class, 'create'])->name('sg.create');

        Route::post('/atualizar/{requisitionId}', [SGController::class, 'update'])->name('sg.update');

        Route::get('/usuarios', [SGController::class, 'users'])->name('sg.users');
        
        Route::get('/pareceres/{requisitionId}', [SGController::class, 'reviews'])->name('sg.reviews');

        Route::get('/escolher-parecerista/{requisitionId}', [SGController::class, 'reviewerPick'])->name('sg.reviewerPick');

        Route::post('/enviar-requerimento/{requisitionId}', [ReviewController::class, 'createReview'])->name('sg.sendToReviewer');

        Route::post('/dar-papel', [RoleController::class, 'addRole'])->name('role.add');

        Route::post('/trocar-papel', [RoleController::class, 'switchRole'])->name('role.switch');

        Route::post('/remover-papel', [RoleController::class, 'removeRole'])->name('role.remove');
    });

    Route::prefix('departamento')->group(function () {
        Route::get('/{departmentName}/lista', [DepartmentController::class, 'list'])->name('department.list');

        Route::get('/detalhe/{requisitionId}', [DepartmentController::class, 'show'])->name('department.show');
    });

    Route::prefix('parecerista')->group(function () {
        Route::get('/lista', function() {
            echo "pagina do parecerista";
        })->name('reviewer.list');
    });
});