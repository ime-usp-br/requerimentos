<?php

namespace App\Http\Controllers;

use App\Models\Requisition;
use Illuminate\Http\Request;
use App\Models\TakenDisciplines;
// use Illuminate\Support\Facades\Auth;
// use Illuminate\Support\Facades\Route;

class SGController extends Controller
{
    public function list() {
        $selectedColumns = ['created_at', 'student_name', 'nusp', 'situation', 'department', 'id'];

        $reqs = Requisition::select($selectedColumns)->get();

        return view('pages.sg.list', ['reqs' => $reqs]);
    }

    public function show($requisitionId) {
        $req = Requisition::with('takenDisciplines')->find($requisitionId);
        
        return view('pages.sg.detail', ['req' => $req, 'takenDiscs' => $req->takenDisciplines]);
    }

    public function create(Request $request) {
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
            'name' => 'required | max:255',
            'email' => 'required | max:255 | email ',
            'nusp' => 'required | numeric | integer',
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
        $req->nusp = $data['nusp'];
        $req->student_name = $data['name'];
        $req->email = $data['email'];
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

        return redirect()->route('sg.newRequisition')->with('success', ['title message' => 'Requerimento criado', 'body message' => 'O requerimento foi criado com sucesso. Acompanhe o andamento pela página inicial.']);
    }

    public function update($requisitionId, Request $request) {
        // dd($request);
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
            'name' => 'required | max:255',
            'email' => 'required | max:255 | email ',
            'nusp' => 'required | numeric | integer',
            'course' => 'required | max:255',
            'requested-disc-name' => 'required | max:255',
            'requested-disc-type' => 'required',
            'requested-disc-code' => 'required',
            'disc-department' => 'required'
        ];

        $data = $request->validate(array_merge($inputArray, $discsArray));
        
        $reqToBeUpdated = Requisition::find($requisitionId);
        $reqToBeUpdated->department = $data['disc-department'];
        $reqToBeUpdated->nusp = $data['nusp'];
        $reqToBeUpdated->student_name = $data['name'];
        $reqToBeUpdated->email = $data['email'];
        $reqToBeUpdated->course = $data['course'];
        $reqToBeUpdated->requested_disc = $data['requested-disc-name'];
        $reqToBeUpdated->requested_disc_type = $data['requested-disc-type'];
        $reqToBeUpdated->requested_disc_code = $data['requested-disc-code'];
        
        // dados vindo direto da request
        $reqToBeUpdated->result = request('result');
        $reqToBeUpdated->result_text = request('result-text');
        $reqToBeUpdated->appraisal = request('appraisal');
        $reqToBeUpdated->reviewer_decision = request('decision');
        $reqToBeUpdated->reviewer_name = request('reviewer_name');
        $reqToBeUpdated->reviewer_nusp = request('reviewer_nusp');
        $reqToBeUpdated->observations = request('observations');

        if ($request->button === 'validate') {
            $reqToBeUpdated->validated_by_sg = true;
        } 

        $reqToBeUpdated->save();

        for ($i = 1; $i <= $takenDiscCount; $i++) {
            $takenDisc = TakenDisciplines::find(request("disc$i-id"));
            $takenDisc->name = $data["disc$i-name"];
            $takenDisc->code = $data["disc$i-code"] ?? "";
            $takenDisc->year = $data["disc$i-year"];
            $takenDisc->grade = $data["disc$i-grade"];
            $takenDisc->semester = $data["disc$i-semester"];
            $takenDisc->institution = $data["disc$i-institution"];
            $takenDisc->requisition_id = $requisitionId;
            $takenDisc->save();
        }

        if ($request->button === 'validate') {
            $bodyMsg = 'O requerimento foi enviado para o departamento';
            $titleMsg = 'Requerimento enviado';
        } elseif ($request->button === 'save') {
            $bodyMsg = 'As informações do requerimento foram salvas';
            $titleMsg = 'Requerimento salvo';        
        }

        return redirect()->route('sg.requisition', ['requisitionId' => request('req-id')])->with('success', ['title message' => $titleMsg, 'body message' => $bodyMsg]);
    }
}
