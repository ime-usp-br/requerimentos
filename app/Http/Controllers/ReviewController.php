<?php

namespace App\Http\Controllers;

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

        if($request->button === 'send'){
            $bodyMsg = 'As informações do requerimento foram salvas';
            $titleMsg = 'Requerimento enviado para secretaria';  
            return redirect()->route('reviewer.show', ['requisitionId' => $requisitionId])->with('sucess', ['title message' => $titleMsg,'body message' => $bodyMsg]);
        };
        
    }
}
