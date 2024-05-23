<?php

namespace App\Http\Controllers;

use App\Enums\DocumentType;
use App\Models\Document;
use App\Models\User;
use App\Models\Event;
use App\Models\Review;
use App\Enums\RoleName;
use App\Enums\EventType;
use App\Models\Requisition;
use Illuminate\Http\Request;
use Spatie\Permission\Models\Role;
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
        $req->validated = true;

        if ($request->name) {
            $event->message = "Enviado para o parecerista " . $request->name;
            $req->internal_status = $event->message;
        }

        $req->save();
        $event->save();

        return response()->noContent();
    }

    public function reviewerPick($requisitionId) {
        $reviewRole = Role::where('name', RoleName::REVIEWER)->first();

        $reviewers = $reviewRole->users;

        return view('pages.reviewer.reviewerPick', ['reviewers' => $reviewers, 'requisitionId' => $requisitionId]);
    }
}
