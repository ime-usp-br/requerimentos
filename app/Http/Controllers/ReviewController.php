<?php

namespace App\Http\Controllers;

use App\Enums\DocumentType;
use App\Models\Document;
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

        $user = Auth::user();
        //$reqReview = Review::where("requisition_id", $requisitionId, "reviewer_nusp", $user->codpes)->first();

        $req = Requisition::with('takenDisciplines', 'documents')->find($requisitionId);
        $selectedColumns = ['student_name', 'nusp','requested_disc' ,'reviewer_decision', 'justification'];
        $reqs = Review::where('reviewer_nusp', $user->codpes)->join('requisitions', 'requisition_id', '=', 'requisitions.id')->select($selectedColumns)->get();
        
        $documents = $req->documents->sortByDesc('created_at');
        // dd($req->documents);

        $takenDiscsRecords = [];
        $currentCourseRecords = [];
        $takenDiscSyllabi = [];
        $requestedDiscSyllabi = [];

        foreach ($documents as $document) {
            switch ($document->type) {
                case DocumentType::TAKEN_DISCS_RECORD:
                    array_push($takenDiscsRecords, $document);
                    break;
                case DocumentType::CURRENT_COURSE_RECORD:
                    array_push($currentCourseRecords, $document);
                    break;
                case DocumentType::TAKEN_DISCS_SYLLABUS:
                    array_push($takenDiscSyllabi, $document);
                    break;
                case DocumentType::REQUESTED_DISC_SYLLABUS:
                    array_push($requestedDiscSyllabi, $document);
                    break;
            }
        }
        
        return view('pages.reviewer.detail', ['req' => $req, 'takenDiscs' => $req->takenDisciplines, 'takenDiscsRecords' => $takenDiscsRecords, 'currentCourseRecords' => $currentCourseRecords, 'takenDiscSyllabi' => $takenDiscSyllabi, 'requestedDiscSyllabi' => $requestedDiscSyllabi, 'reqs' =>$reqs]);

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

        return response()->noContent();
    }

    public function update($requisitionId, Request $request){
        $user = Auth::user();

        $reqToBeUpdated = Review::where("requisition_id", $requisitionId)->where("reviewer_nusp", $user->codpes)->first();
        //dd($reqToBeUpdated);
            // dd($data);
        $reqRequisition = Requisition::find($requisitionId);
   

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


        if($request->button === 'send'){
            $bodyMsg = 'As informações do requerimento foram salvas';
            $titleMsg = 'Requerimento enviado para secretaria';  
            return redirect()->route('reviewer.list', ['requisitionId' => $requisitionId])->with('sucess', ['title message' => $titleMsg,'body message' => $bodyMsg]);
        };
    }
}
