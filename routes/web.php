<?php

use App\Http\Controllers\SGController;
use Illuminate\Support\Facades\Route;
use App\Http\Controllers\StudentController;

Route::get('/', function () {
    return view('pages.home');
});

Route::get('/acesso-negado', function() {
    return view('pages.accessDenied');
});

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
    });


});