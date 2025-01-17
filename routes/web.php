<?php

use App\Models\User;
use App\Enums\RoleName;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\Route;
use App\Http\Controllers\SGController;
use App\Http\Controllers\RoleController;
use App\Http\Controllers\GlobalController;
use App\Http\Controllers\RecordController;
use App\Http\Controllers\ReviewController;
use App\Http\Controllers\StudentController;
use App\Http\Controllers\DepartmentController;
use App\Http\Controllers\RequisitionController;
use App\Notifications\RequisitionResultNotification;

Route::get('/', function () {
    return view('pages.home');
});

// Route::get('/notification', function () {
//     // $loggedUser = Auth::user();
//     // return view('pages.accessDenied');
//     // dd('ola');
//     $studentUser = User::where('codpes', 10758748)->first();

//     $studentUser->notify(new RequisitionResultNotification($studentUser));

//     // dump('ola');
//     return 'Email enviado';
// });

// rota usada para achar informações sobre a configuração de php da máquina
Route::get('/phpinfo', function () {
    phpinfo();
});

Route::get('/acesso-negado', function() {
    return view('pages.accessDenied');
});

Route::get('/callback', [GlobalController::class, 'callbackHandler']);

Route::get('/documento/{documentId}', [GlobalController::class, 'documentHandler'])->name('document.show');

Route::middleware('auth')->group(function() {
    
    Route::prefix('aluno')->middleware('role:' . RoleName::STUDENT)->group(function () {
    // Route::prefix('aluno')->group(function () {
        Route::get('/lista', [StudentController::class, 'list'])->name('student.list');
        
        Route::view('/novo-requerimento', 'pages.student.newRequisition')->name('student.newRequisition')
            ->middleware('requisitions.period');
        
        Route::post('/novo-requerimento', [StudentController::class, 'create'])->name('student.create')
            ->middleware('requisitions.period');
        
        Route::get('/detalhe/{requisitionId}', [StudentController::class, 'show'])->name('student.show');

        Route::get('/atualizar/{requisitionId}', [StudentController::class, 'show'])->name('student.edit');

        Route::post('/atualizar/{requisitionId}', [StudentController::class, 'update'])->name('student.update');
    });

    Route::prefix('sg')->middleware('role:' . RoleName::SG)->group(function () {
    // Route::prefix('sg')->group(function () {
        Route::get('/exporta-csv', [RequisitionController::class, 'exportCSV'])->name('export.csv');

        Route::get('/lista', [SGController::class, 'list'])->name('sg.list');

        Route::view('/novo-requerimento', 'pages.sg.newRequisition')->name('sg.newRequisition');

        Route::get('/detalhe/{requisitionId}', [SGController::class, 'show'])->name('sg.show');

        Route::post('/novo-requerimento', [SGController::class, 'create'])->name('sg.create');

        Route::post('/atualizar/{requisitionId}', [SGController::class, 'update'])->name('sg.update');

        Route::post('/remover-papel', [RoleController::class, 'removeRole'])->name('role.remove');

        Route::get('/filters', [RequisitionController::class, 'showFilters'])->name('pages.requisitions.filters');
        
        Route::get('/export', [RequisitionController::class, 'filterAndExport'])->name('pages.requisitions.filterAndExport');

        Route::get('/admin', [SGController::class, 'admin'])->name('sg.admin');
        
        Route::post('/periodo-requerimento', [SGController::class, 'requisition_period_toggle'])->name('sg.requisition_period_toggle');

    });

    Route::prefix('departamento')->middleware("role:" . RoleName::MAC_SECRETARY . 
                                                  ',' . RoleName::MAT_SECRETARY . 
                                                  ',' . RoleName::MAE_SECRETARY . 
                                                  ',' . RoleName::MAP_SECRETARY .
                                                  ',' . RoleName::VRT_SECRETARY)->group(function () {
    // Route::prefix('departamento')->group(function () {
        Route::get('/{departmentName}/lista', [DepartmentController::class, 'list'])->name('department.list');

        Route::get('/{departmentName}/detalhe/{requisitionId}', [DepartmentController::class, 'show'])->name('department.show');

        Route::get('/{departmentName}/usuarios', [DepartmentController::class, 'users'])->name('department.users');
    });
    
    Route::prefix('parecerista')->middleware('role:'. RoleName::REVIEWER)->group(function () {
    // Route::prefix('parecerista')->group(function () {
        Route::get('/lista', [ReviewController::class, 'list'])->name('reviewer.list');

        Route::get('/detalhe/{requisitionId}', [ReviewController::class, 'show'])->name('reviewer.show');
        
        Route::post('/salvar-ou-enviar/{requisitionId}', [ReviewController::class, 'saveOrSubmit'])->name('reviewer.saveOrSubmit');

        Route::get('/detalhe/{requisitionId}/pareceres-anteriores/{requestedDiscCode}', [ReviewController::class, 'previousReviews'])->name('geral.previousReviews');

        Route::post('/copiar/{requisitionId}', [ReviewController::class, 'copy'])->name('reviewer.copy');
    });
    // });
    
    
    Route::group(['middleware' => "role:" . RoleName::SG . 
                                      ',' . RoleName::REVIEWER .
                                      ',' . RoleName::MAC_SECRETARY . 
                                      ',' . RoleName::MAT_SECRETARY . 
                                      ',' . RoleName::MAE_SECRETARY . 
                                      ',' . RoleName::MAP_SECRETARY .
                                      ',' . RoleName::VRT_SECRETARY], function () {
        // Cuidado!! Da forma como isso está construído, qualquer pessoa dentre essas roles, 
        // se agir de maneira maliciosa consegue assumir qualquer outro papel.
        // É necessário fazer ajustes de segurança em RoleController->switchRole
        Route::post('/trocar-papel', [RoleController::class, 'switchRole'])->name('role.switch');
        
        Route::post('/dar-papel', [RoleController::class, 'addRole'])->name('role.add');
        
        Route::post('/remover-papel', [RoleController::class, 'removeRole'])->name('role.remove');

        Route::get('/escolher-parecerista/{requisitionId}', [ReviewController::class, 'reviewerPick'])->name('reviewer.reviewerPick');
        
        Route::get('/pareceres/{requisitionId}', [ReviewController::class, 'reviews'])->name('reviewer.reviews');

        Route::post('/enviar-requerimento/{requisitionId}', [ReviewController::class, 'createReview'])->name('reviewer.sendToReviewer');

        Route::get('/historico/requerimento/{requisitionId}', [RecordController::class, 'requisitionRecord'])->name('record.requisition');

        Route::get('/historico/versao/{eventId}', [RecordController::class, 'requisitionVersion'])->name('record.requisitionVersion');

        Route::post('/cadastrado/{requisitionId}', [DepartmentController::class, 'registered'])->name('department.registered');

    });
});
