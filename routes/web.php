<?php

use Illuminate\Support\Facades\Route;
use App\Http\Controllers\RequisitionController;

Route::get('/', function () {
    return view('pages.home');
});

Route::get('/acesso-negado', function() {
    return view('pages.accessDenied');
});

Route::middleware('auth')->group(function() {
    Route::get('/lista', [RequisitionController::class, 'list'])->name('requisitions.list');

    Route::view('/novo-requerimento', 'pages.newRequisition')->name('newRequisition');

    Route::get('/detalhe/{requisitionId}', [RequisitionController::class, 'show'])->name('requisitions.show');

    Route::post('/novo-requerimento', [RequisitionController::class, 'create'])->name('requisitions.create');

    Route::get('/atualizar-requerimento/{requisitionId}', [RequisitionController::class, 'show'])->name('requisitions.edit');

    Route::post('/atualizar/{requisitionId}', [RequisitionController::class, 'update'])->name('requisitions.update');
});