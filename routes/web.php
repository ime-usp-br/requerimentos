<?php

use App\Models\User;
use App\Models\Requisition;
use App\Models\TakenDisciplines;
use Illuminate\Support\Facades\Route;
use Illuminate\Http\Request;
/*
|--------------------------------------------------------------------------
| Web Routes
|--------------------------------------------------------------------------
|
| Here is where you can register web routes for your application. These
| routes are loaded by the RouteServiceProvider within a group which
| contains the "web" middleware group. Now create something great!
|
*/

Route::get('/', function () {
    return view('pages.home');
});

Route::get('/acesso-negado', function() {
    return view('pages.accessDenied');
});

Route::get('/lista', function () {

    $user = Auth::user();

    if ($user->name === 'Guilherme Simoes Santos Marin') {
        $reqs = Requisition::with('takenDisciplines')->select('created_at', 'requested_disc', 'nusp', 'situation', 'id')->where('nusp', Auth::user()->codpes)->get();

        return view('pages.list', ['reqs' => $reqs]);
    } elseif ($user->name === 'João') {
        $reqs = Requisition::with('takenDisciplines')->select('created_at', 'requested_disc', 'nusp', 'situation', 'id')->where('nusp', Auth::user()->codpes)->get();

        return view('pages.list', ['reqs' => $reqs]);
    }
    
})->middleware('auth')->name('list');

Route::get('/novo-requerimento', function() {
    return view('pages.newRequisition');
})->middleware('auth')->name('newRequisition');

Route::get('/analise/{requisitionId}', function($requisitionId) {

    $req = Requisition::with('takenDisciplines')->find($requisitionId);
    
    return view('pages.requisitionDetails', ['req' => $req, 'takenDiscs' => $req->takenDisciplines]);
})->middleware('auth')->name('requisitions.show');

Route::post('/novo-requerimento', function(Request $request) {
    // dd($request->input('takenDiscCount'));
    $takenDiscCount = (int) $request->takenDiscCount;
    $discsArray = [];

    for ($i = 1; $i <= $takenDiscCount; $i++) {
        $discsArray["disc$i-name"] = 'required | max:255';
        $discsArray["disc$i-code"] = 'max:255';
        $discsArray["disc$i-year"] = 'required | numeric | integer';
        $discsArray["disc$i-grade"] = 'required | numeric';
        $discsArray["disc$i-semester"] = 'required';
        $discsArray["disc$i-institution"] = 'required';
    }

    $inputArray = [
        'course' => 'required | max:255',
        'requested-disc-name' => 'required | max:255',
        'requested-disc-type' => 'required',
        'requested-disc-code' => 'required',
        'taken-disc-record' => 'required | file | mimes:pdf',
        'course-record' => 'required | file | mimes:pdf',
        'taken-disc-syllabus' => 'required | file | mimes:pdf',
        'requested-disc-syllabus' => 'required | file | mimes:pdf',
        'disc-department' => 'required'
    ];

    $data = $request->validate(array_merge($inputArray, $discsArray));

    $req = new Requisition;
    $req->department = $data['disc-department'];
    $req->nusp = Auth::user()->codpes;
    $req->student_name = Auth::user()->name;
    $req->email = Auth::user()->email;
    $req->course = $data['course'];
    $req->requested_disc = $data['requested-disc-name'];
    $req->requested_disc_type = $data['requested-disc-type'];
    $req->requested_disc_code = $data['requested-disc-code'];
    $req->situation = "Encaminhado para a secretaria";
    $req->reviewer_name = null;
    $req->result = 'Sem resultado';
    $req->result_text = null;
    $req->appraisal = null;
    $req->reviewer_nusp = null;
    $req->reviewer_decision = 'Sem decisão';
    $req->taken_discs_record = $request->file('taken-disc-record')->store('test');
    $req->current_course_record = $request->file('course-record')->store('test');
    $req->taken_discs_syllabus = $request->file('taken-disc-syllabus')->store('test');
    $req->requested_disc_syllabus = $request->file('requested-disc-syllabus')->store('test');
    $req->observations = $request->observations;
    $req->validated_by_sg = false;

    $req->save();

    for ($i = 1; $i <= $takenDiscCount; $i++) {
        $takenDisc = new TakenDisciplines;
        $takenDisc->name = $data["disc$i-name"];
        $takenDisc->code = $data["disc$i-code"] ?? "";
        $takenDisc->year = $data["disc$i-year"];
        $takenDisc->grade = $data["disc$i-grade"];
        $takenDisc->semester = $data["disc$i-semester"];
        $takenDisc->institution = $data["disc$i-institution"];
        $takenDisc->requisition_id = $req->id;
        $takenDisc->save();
    }

    return redirect()->route('newRequisition')->with('success', ['title message' => 'Requerimento criado', 'body message' => "O requerimento foi criado com sucesso. Acompanhe o andamento pelo campo 'situação' na página inicial."]);

})->name('requisitions.create');