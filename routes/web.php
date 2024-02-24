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

Route::get('/roteador-usuario', function() {
    $user = Auth::user();

    if ($user->name === 'Guilherme Simoes Santos Marin') {
        return redirect()->route('student.list');
    } elseif ($user->name === 'João') {
        return redirect()->route('sg.list');
    }
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

        Route::view('/novo-requerimento', 'pages.sg.newRequisition')->name('sg.newRequisition');

        Route::post('/novo-requerimento', [SGController::class, 'create'])->name('sg.create');

        
    });


});