<?php

use Inertia\Inertia;
use App\Enums\RoleName;
use App\Http\Controllers\AdminController;
use Illuminate\Support\Facades\Route;

use App\Http\Controllers\ListController;
use App\Http\Controllers\RequisitionController;
use App\Http\Controllers\LoginController;

use App\Http\Controllers\SGController;
use App\Http\Controllers\RoleController;
use App\Http\Controllers\ReviewController;
use App\Http\Controllers\RecordController;
// use App\Http\Controllers\GlobalController;
// use App\Http\Controllers\StudentController;
use App\Http\Middleware\CheckCurrentRole;

// use App\Http\Middleware\CheckRequisitionsPeriod;

Route::get('/', function () {
    return Inertia::render('Home');
})->name('home');

Route::get('/login', [LoginController::class, 'redirectToProvider'])->name('login');
Route::get('/callback', [LoginController::class, 'callbackHandler']);
Route::get('/logout', [LoginController::class, 'logout'])->name('logout');


// rota usada para achar informações sobre a configuração de php da máquina
Route::get('/phpinfo', function () {
    phpinfo();
});

// Route::get('/documento/{documentId}', [GlobalController::class, 'documentHandler'])->name('document.show');

Route::middleware('auth')->group(function () {
    Route::get('/lista', [ListController::class, 'list'])->name('list');
    Route::get('/novo-requerimento', [RequisitionController::class, 'newRequisitionGet'])->name('newRequisition.get');
        // ->middleware('requisitions.period');
    Route::post('/novo-requerimento', [RequisitionController::class, 'newRequisitionPost'])->name('newRequisition.post');
        // ->middleware('requisitions.period');
    Route::get('/atualizar-requerimento/{requisitionId}', [RequisitionController::class, 'updateRequisitionGet'])->name('updateRequisition.get');
    Route::post('/atualizar-requerimento', [RequisitionController::class, 'updateRequisitionPost'])->name('updateRequisition.post');
    Route::get('/detalhe/{requisitionId}', [RequisitionController::class, 'showRequisition'])->name('showRequisition');
    Route::post('/trocar-papel', [RoleController::class, 'switchRole'])->name('role.switch');
    
    Route::group(['middleware' => CheckCurrentRole::class . ":" . RoleName::SG], function () {
        Route::get('/status-periodo-requerimento', [AdminController::class, 'getRequisitionPeriodStatus'])->name('admin.getRequisitionPeriodStatus');
        Route::post('/alterar-periodo-requerimento', [AdminController::class, 'setRequisitionPeriodStatus'])->name('admin.setRequisitionPeriodStatus');
    });
    
    Route::group(['middleware' => CheckCurrentRole::class . ":" . RoleName::SG .
                                                            ',' . RoleName::SECRETARY], function () {
        Route::get('/admin', [AdminController::class, 'admin'])->name('admin');
        Route::get('/ver-papeis', [RoleController::class, 'listRolesAndDepartments'])->name('role.listRolesAndDepartments');
        Route::post('/dar-papel', [RoleController::class, 'addRole'])->name('role.add');
        Route::post('/remover-papel', [RoleController::class, 'removeRole'])->name('role.remove');
        Route::get('/escolher-parecerista', [ReviewController::class, 'reviewerPick'])->name('reviewer.reviewerPick');
        Route::post('/cadastrado/{requisitionId}', [RequisitionController::class, 'registered'])->name('registered');
        Route::get('/exportar-requerimentos', [RequisitionController::class, 'exportRequisitionsGet'])->name('exportRequisitionsGet');
        Route::post('/exportar-requerimentos', [RequisitionController::class, 'exportRequisitionsPost'])->name('exportRequisitionsPost');
    });
    
    Route::group(['middleware' => CheckCurrentRole::class . ":" . RoleName::SG .
                                                            ',' . RoleName::SECRETARY .
                                                            ',' . RoleName::REVIEWER], function () {
        Route::get('/pareceres/{requisitionId}', [ReviewController::class, 'reviews'])->name('reviewer.reviews');
        Route::post('/enviar-requerimento', [ReviewController::class, 'createReview'])->name('reviewer.sendToReviewer');
        Route::get('/historico/requerimento/{requisitionId}', [RecordController::class, 'requisitionRecord'])->name('record.requisition');
        Route::post('/enviar-ao-departamento', [RequisitionController::class, 'sendToDepartment'])->name('sendToDepartment');
    });
});