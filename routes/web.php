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
    // // Route::prefix('sg')->middleware(CheckCurrentRole::class . ":" . RoleName::SG)->group(function () {
    // Route::prefix('sg')->group(function () {

        // Route::get('/exporta-csv', [RequisitionController::class, 'exportCSV'])->name('export.csv');

        // Route::post('/remover-papel', [RoleController::class, 'removeRole'])->name('role.remove');


        // Route::get('/export', [RequisitionController::class, 'filterAndExport'])->name('pages.requisitions.filterAndExport');

        // Route::get('/admin', [SGController::class, 'admin'])->name('sg.admin');

        // Route::post('/periodo-requerimento', [SGController::class, 'requisition_period_toggle'])->name('sg.requisition_period_toggle');

    // });

    // Route::prefix('departamento')->middleware(CheckCurrentRole::class . ":" . RoleName::MAC_SECRETARY . 
    //                                               ',' . RoleName::MAT_SECRETARY . 
    //                                               ',' . RoleName::MAE_SECRETARY . 
    //                                               ',' . RoleName::MAP_SECRETARY .
    //                                               ',' . RoleName::VRT_SECRETARY)->group(function () {
    // // Route::prefix('departamento')->group(function () {

    //     Route::get('/{departmentName}/usuarios', [DepartmentController::class, 'users'])->name('department.users');
    // });

    // Route::prefix('parecerista')->middleware(CheckCurrentRole::class . ":" . RoleName::REVIEWER)->group(function () {
    // // Route::prefix('parecerista')->group(function () {
    //     Route::post('/salvar-ou-enviar/{requisitionId}', [ReviewController::class, 'saveOrSubmit'])->name('reviewer.saveOrSubmit');

    //     Route::get('/detalhe/{requisitionId}/pareceres-anteriores/{requestedDiscCode}', [ReviewController::class, 'previousReviews'])->name('geral.previousReviews');

    //     Route::post('/copiar/{requisitionId}', [ReviewController::class, 'copy'])->name('reviewer.copy');
    // });
    // // });

    //     // Cuidado!! Da forma como isso está construído, qualquer pessoa dentre essas roles, 
    //     // se agir de maneira maliciosa consegue assumir qualquer outro papel.
    //     // É necessário fazer ajustes de segurança em RoleController->switchRole
    //     Route::post('/trocar-papel', [RoleController::class, 'switchRole'])->name('role.switch');



    //     Route::get('/escolher-parecerista/{requisitionId}', [ReviewController::class, 'reviewerPick'])->name('reviewer.reviewerPick');

    //     Route::get('/pareceres/{requisitionId}', [ReviewController::class, 'reviews'])->name('reviewer.reviews');

    //     Route::post('/enviar-requerimento/{requisitionId}', [ReviewController::class, 'createReview'])->name('reviewer.sendToReviewer');

    //     Route::get('/historico/requerimento/{requisitionId}', [RecordController::class, 'requisitionRecord'])->name('record.requisition');

    //     Route::get('/historico/versao/{eventId}', [RecordController::class, 'requisitionVersion'])->name('record.requisitionVersion');

    //     Route::post('/cadastrado/{requisitionId}', [DepartmentController::class, 'registered'])->name('department.registered');

    // });