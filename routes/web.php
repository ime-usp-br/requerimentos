<?php

use Inertia\Inertia;
use App\Enums\RoleId;
use App\Http\Controllers\AdminController;
use Illuminate\Support\Facades\Route;

use App\Http\Controllers\ListController;
use App\Http\Controllers\RequisitionController;
use App\Http\Controllers\LoginController;

use App\Http\Controllers\RoleController;
use App\Http\Controllers\ReviewController;
use App\Http\Controllers\RecordController;

use App\Http\Controllers\DocumentsController;

Route::get('/', function () {
    return Inertia::render('Home');
})->name('home');

Route::get('/login', [LoginController::class, 'redirectToProvider'])
    ->name('login');
Route::get('/callback', [LoginController::class, 'callbackHandler']);
Route::get('/logout', [LoginController::class, 'logout'])
    ->name('logout');


// rota usada para achar informações sobre a configuração de php da máquina
Route::get('/phpinfo', function () {
    phpinfo();
});

// Route::get('/documento/{documentId}', [GlobalController::class, 'documentHandler'])
    // ->name('document.show');

// ======== ACESSO AUTENTICADO ======== //
Route::middleware('auth')->group(function () {
    Route::get('/lista', [ListController::class, 'list'])
        ->name('list');
    Route::get('/novo-requerimento', [RequisitionController::class, 'newRequisitionGet'])
        ->name('newRequisition.get')
        ->middleware('check.requisitions.period:creation');
    Route::post('/novo-requerimento', [RequisitionController::class, 'newRequisitionPost'])
        ->name('newRequisition.post')
        ->middleware('check.requisitions.period:creation');
    Route::get('/atualizar-requerimento/{requisitionId}', [RequisitionController::class, 'updateRequisitionGet'])
        ->name('updateRequisition.get')
        ->middleware('check.requisitions.period:edition');
    Route::post('/atualizar-requerimento', [RequisitionController::class, 'updateRequisitionPost'])
        ->name('updateRequisition.post')
        ->middleware('check.requisitions.period:edition');
    Route::get('/detalhe/{requisitionId}', [RequisitionController::class, 'showRequisition'])
        ->name('showRequisition');
    Route::post('/trocar-papel', [RoleController::class, 'switchRole'])
        ->name('role.switch');
    Route::get('/status-periodo-requerimento', [AdminController::class, 'getRequisitionPeriodStatus'])
        ->name('getRequisitionPeriodStatus');
    Route::get('/documents/{id}/view', [DocumentsController::class, 'view'])->name('documents.view');

    // ======== ACESSO SG + Secretarias + Pareceristas ======== //
    Route::group(['middleware' => 'check.current.role:' . implode(',', [RoleId::SG, RoleId::SECRETARY, RoleId::REVIEWER])], function () {
        Route::get('/pareceres/{requisitionId}', [ReviewController::class, 'reviews'])
            ->name('reviewer.reviews');
        Route::post('/enviar-requerimento', [ReviewController::class, 'createReview'])
            ->name('reviewer.sendToReviewer');
        Route::get('/historico/requerimento/{requisitionId}', [RecordController::class, 'requisitionRecord'])
            ->name('record.requisition');
        Route::post('/enviar-ao-departamento', [RequisitionController::class, 'sendToDepartment'])
            ->name('sendToDepartment');
    });

    // ======== ACESSO SG + Secretarias ======== //
    Route::group(['middleware' => 'check.current.role:' . implode(',', [RoleId::SG, RoleId::SECRETARY])], function () {
        Route::get('/admin', [AdminController::class, 'admin'])
            ->name('admin');
        Route::get('/ver-papeis', [RoleController::class, 'listRolesAndDepartments'])
            ->name('role.listRolesAndDepartments');
        Route::post('/dar-papel', [RoleController::class, 'addRole'])
            ->name('role.add');
        Route::post('/remover-papel', [RoleController::class, 'removeRole'])
            ->name('role.remove');
        Route::get('/escolher-parecerista', [ReviewController::class, 'reviewerPick'])
            ->name('reviewer.reviewerPick');
        Route::post('/cadastrado', [RequisitionController::class, 'registered'])
            ->name('registered');
        Route::get('/exportar-requerimentos', [RequisitionController::class, 'exportRequisitionsGet'])
            ->name('exportRequisitionsGet');
        Route::post('/exportar-requerimentos', [RequisitionController::class, 'exportRequisitionsPost'])
            ->name('exportRequisitionsPost');
    });

    // ======== ACESSO SG ======== //
    Route::group(['middleware' => 'check.current.role:' . RoleId::SG], function () {
        Route::post('/alterar-periodo-requerimento', [AdminController::class, 'setRequisitionPeriodStatus'])
            ->name('admin.setRequisitionPeriodStatus');
        Route::post('/dar-resultado-ao-requerimento', [RequisitionController::class, 'setRequisitionResult'])
            ->name('giveResultToRequisition');
    });
});