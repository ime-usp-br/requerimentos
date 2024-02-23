<?php

namespace App\Http\Controllers;

use App\Models\Requisition;
use Illuminate\Http\Request;
use App\Models\TakenDisciplines;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\Route;

class RequisitionController extends Controller
{
    public function list() {
        $user = Auth::user();

        if ($user->name === 'Guilherme Simoes Santos Marin') {
            $reqs = Requisition::with('takenDisciplines')->select('created_at', 'requested_disc', 'nusp', 'situation', 'id')->where('nusp', $user->codpes)->get();

            return view('pages.list', ['reqs' => $reqs]);
        } elseif ($user->name === 'João') {
            $reqs = Requisition::with('takenDisciplines')->select('created_at', 'requested_disc', 'nusp', 'situation', 'id')->where('nusp', $user->codpes)->get();

            return view('pages.list', ['reqs' => $reqs]);
        }
    }

    public function show($requisitionId) {
        // if ($req === null) {
        // }
        // elseif (Gate::denies('see-requisition', $req)) {
        //     abort(403);
        // }
        $user = Auth::user();

        if ($user->name === 'Guilherme Simoes Santos Marin') {
            $req = Requisition::with('takenDisciplines')->find($requisitionId);

            $routeName = Route::currentRouteName();

            if ($routeName === 'requisitions.show') {
                return view('pages.requisitionDetails', ['req' => $req, 'takenDiscs' => $req->takenDisciplines]);
            } elseif ($routeName === 'requisitions.edit') {
                return view('pages.editRequisition', ['req' => $req, 'takenDiscs' => $req->takenDisciplines]);
            }
            
        } elseif ($user->name === 'João') {
            abort(403);
            // $reqs = Requisition::all();
        }
    }

    public function create(Request $request) {
        $user = Auth::user();

        if ($user->name === 'Guilherme Simoes Santos Marin') {
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
            $req->nusp = $user->codpes;
            $req->student_name = $user->name;
            $req->email = $user->email;
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
        } elseif ($user->name === 'João') {
            abort(403);
        }
    }

    public function update(Request $request, $requisitionId) {
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
            'requested-disc-code' => 'required | max:255',
            'disc-department' => 'required',
            'taken-disc-record' => 'required | file | mimes:pdf',
            'course-record' => 'required | file | mimes:pdf',
            'taken-disc-syllabus' => 'required | file | mimes:pdf',
            'requested-disc-syllabus' => 'required | file | mimes:pdf',
        ];

        $data = $request->validate(array_merge($inputArray, $discsArray));
        
        $reqToBeUpdated = Requisition::find($requisitionId);
        $reqToBeUpdated->department = $data['disc-department'];
        $reqToBeUpdated->course = $data['course'];
        $reqToBeUpdated->requested_disc = $data['requested-disc-name'];
        $reqToBeUpdated->requested_disc_type = $data['requested-disc-type'];
        $reqToBeUpdated->requested_disc_code = $data['requested-disc-code'];
        $reqToBeUpdated->observations = request('observations');
        $reqToBeUpdated->taken_discs_record = $request->file('taken-disc-record')->store('test');
        $reqToBeUpdated->current_course_record = $request->file('course-record')->store('test');
        $reqToBeUpdated->taken_discs_syllabus = $request->file('taken-disc-syllabus')->store('test');
        $reqToBeUpdated->requested_disc_syllabus = $request->file('requested-disc-syllabus')->store('test');
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

        return redirect()->route('requisitions.edit', ['requisitionId' => $requisitionId])->with('success', ['title message' => 'Requerimento salvo', 'body message' => 'As novas informações do requerimento foram salvas com sucesso']);
    }

}
