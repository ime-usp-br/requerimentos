<?php

namespace App\Http\Controllers;

use App\Models\User;
use App\Models\Event;
use App\Models\Requisition;
use App\Models\TakenDisciplines;
use App\Models\Review;
use App\Enums\EventType;
use Spatie\Permission\Models\Role;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;

class ReviewController extends Controller
{
    public function list() {
        $user = Auth::user();

        $selectedColumns = ['requisitions.created_at', 'student_name', 'nusp','requested_disc' ,'requisitions.id'];
        $selectId =['created_at', 'requisition_id' ] ;

        //pegando os valores de 
        $reqs = Review::where('reviewer_nusp', $user->codpes)->join('requisitions', 'requisition_id', '=', 'requisitions.id')->select($selectedColumns)->get();
        //$reqs = Review::where('reviewer_nusp', $user->codpes)->leftJoin('requisitions', 'review.requisition_id', '=', 'id')->select($selectedColumns)->get();
    
        //$reqs = Requisition::select($selectedColumns)->where('id', $teste[0])->get();

        return view('pages.reviewer.list', ['reqs' => $reqs]);
    }

    public function show($requisitionId){
        $req = Requisition::with('takenDisciplines')->find($requisitionId);
        
        return view('pages.reviewer.detail', ['req' => $req, 'takenDiscs' => $req->takenDisciplines]);

    }

    public function createReview($requisitionId, Request $request) {
        
        Review::firstOrCreate(['reviewer_nusp' => $request->nusp, 'requisition_id' => $requisitionId], ['reviewer_decision' => 'Sem decisão', 'requisition_id' => $requisitionId, 'justification' => null, 'reviewer_nusp' => $request->nusp, 'reviewer_name' => $request->name]);

        $user = Auth::user();
        $event = new Event;
        $event->type = EventType::SENT_TO_REVIEWERS;
        $event->requisition_id = $requisitionId;
        $event->author_name = $user->name; 
        $event->author_nusp = $user->codpes;

        $req = Requisition::find($requisitionId);
        $req->situation = EventType::SENT_TO_REVIEWERS;

        if ($request->name) {
            $event->message = "Enviado para o parecerista " . $request->name;
            $req->internal_status = $event->message;
        }

        $req->save();
        $event->save();


        // $review = new Review;
        // $review->reviewer_decision = 'Sem decisão';
        // $review->requisition_id = $requisitionId;
        // $review->justification = null;
        // $review->reviewer_nusp = $request->nusp;
        // $review->reviewer_name = $request->name;
        // $review->save();

        return response()->noContent();
    }

    public function update($requisitionId, Request $request){
        $reqToBeUpdated = Review::find($requisitionId);

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
            'disc-department' => 'required',
            'appraisal' => 'required',
            'decision' => "required"
        ];
        $data = $request->validate(array_merge($inputArray, $discsArray));
            // dd($data);
        $reqRequisition = Requisition::find($requisitionId);
        $reqRequisition->department = $data['disc-department'];
        $reqRequisition->nusp = $data['nusp'];
        $reqRequisition->student_name = $data['name'];
        $reqRequisition->email = $data['email'];
        $reqRequisition->course = $data['course'];
        $reqRequisition->requested_disc = $data['requested-disc-name'];
        $reqRequisition->requested_disc_type = $data['requested-disc-type'];
        $reqRequisition->requested_disc_code = $data['requested-disc-code'];
        //dd($reqToBeUpdated);

        if($reqToBeUpdated->reviewer_decision !== request('decision')){
            $reqToBeUpdated->reviewer_decision = request('decision');

            $user = Auth::user();
            if(request('decision') === 'Sem decisão'){
                $tipo = EventType::IN_REVALUATION;
            }
            elseif(request('decision') === 'Deferido'){
                $tipo = EventType::ACCEPTED;
            }
            elseif(request('decision') === 'Indeferido'){
                $tipo = EventType::REJECTED;
            }
            $event = new Event;
            $event->type = $tipo;
            $event->requisition_id = $requisitionId;
            $event->author_name = Auth::user()->name; 
            $event->author_nusp = Auth::user()->codpes;
            $reqRequisition->situation = EventType::RETURNED_BY_REVIEWER;
            $reqRequisition->internal_status = EventType::RETURNED_BY_REVIEWER;
            $event->save();
        }

        $reqToBeUpdated->justification = request('appraisal');

        $reqToBeUpdated->save();

        $reqRequisition->save();
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


        if($request->button === 'send'){
            $bodyMsg = 'As informações do requerimento foram salvas';
            $titleMsg = 'Requerimento enviado para secretaria';  
            return redirect()->route('reviewer.list', ['requisitionId' => $requisitionId])->with('sucess', ['title message' => $titleMsg,'body message' => $bodyMsg]);
        };
    }
}
