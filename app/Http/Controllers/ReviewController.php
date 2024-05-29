<?php

namespace App\Http\Controllers;

use App\Models\Event;
use App\Models\Review;
use App\Enums\RoleName;
use App\Enums\EventType;
use App\Enums\DocumentType;
use App\Models\Requisition;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\DB;
use Spatie\Permission\Models\Role;
use Illuminate\Support\Facades\Auth;

class ReviewController extends Controller
{
    public function list() {

        $user = Auth::user();

        $selectedColumns = ['requisitions.created_at', 'student_name', 'nusp','requested_disc','requisitions.id'];

        // $reqs = Review::where('reviewer_nusp', $user->codpes)->join('requisitions', 'requisition_id', '=', 'requisitions.id')->select($selectedColumns)->get();

        $reqs = DB::table('reviews')->join('requisitions', 'reviews.requisition_id', '=', 'requisitions.id')->where('reviewer_nusp', $user->codpes)->select($selectedColumns)->get();


        return view('pages.reviewer.list', ['reqs' => $reqs]);
    }

    public function show($requisitionId) {

        $user = Auth::user();

        $req = Requisition::with('takenDisciplines', 'documents')->find($requisitionId);

        $review = Review::where('reviewer_nusp', $user->codpes)->where('requisition_id', $requisitionId)->first();
        
        $documents = $req->documents->sortByDesc('created_at');

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
        
        return view('pages.reviewer.detail', ['req' => $req, 'takenDiscs' => $req->takenDisciplines, 'takenDiscsRecords' => $takenDiscsRecords, 'currentCourseRecords' => $currentCourseRecords, 'takenDiscSyllabi' => $takenDiscSyllabi, 'requestedDiscSyllabi' => $requestedDiscSyllabi, 'review' => $review]);

    }

    public function createReview($requisitionId, Request $request) {
        
        DB::transaction(function() use ($request, $requisitionId) {

            Review::firstOrCreate(['reviewer_nusp' => $request->nusp, 'requisition_id' => $requisitionId], ['reviewer_decision' => 'Sem decisão', 'requisition_id' => $requisitionId, 'justification' => null, 'reviewer_nusp' => $request->nusp, 'reviewer_name' => $request->name]);

            $user = Auth::user();

            $req = Requisition::find($requisitionId);

            // $req->situation é o que aparece na linha do requerimento na tabela 
            // para o aluno (não contém o nome do parecerista)
            $req->situation = EventType::SENT_TO_REVIEWERS;
            $req->validated = true;

            $event = new Event;
            $event->type = EventType::SENT_TO_REVIEWERS;
            $event->requisition_id = $requisitionId;
            $event->author_name = $user->name; 
            $event->author_nusp = $user->codpes;
            $event->version = $req->latest_version;

            if ($request->name) {
                // a $event->message/$req->internal_status contém o nome do  
                // parecerista, mas não aparece para o aluno, é usada apenas  
                // internamente
                $event->message = "Enviado para o parecerista " . $request->name;
                $req->internal_status = $event->message;
            }

            $req->save();
            $event->save();
        });
        
        return response()->noContent();
    }

    public function update($requisitionId, Request $request) {

        DB::transaction(function() use ($request, $requisitionId) {

            $user = Auth::user();

            $reviewToBeUpdated = Review::where('requisition_id', $requisitionId)->where('reviewer_nusp', $user->codpes)->first();

            $reviewToBeUpdated->reviewer_decision = $request->decision;
            $reviewToBeUpdated->justification = $request->justification;
            $reviewToBeUpdated->save();

            $req = Requisition::find($requisitionId);
            $req->situation = EventType::RETURNED_BY_REVIEWER;

            $event = new Event;
            $event->type = EventType::RETURNED_BY_REVIEWER;
            $event->requisition_id = $requisitionId;
            $event->author_name = $user->name; 
            $event->author_nusp = $user->codpes;
            $event->version = $req->latest_version;

            $event->message = "Retornado pelo parecerista " . $user->name;
            $req->internal_status = $event->message;

            $event->save();
            $req->save();
        });

        $bodyMsg = 'As informações do parecer foram salvas';
        $titleMsg = 'Parecer salvo';  

        return redirect()->route('reviewer.show', ['requisitionId' => $requisitionId])->with('success', ['title message' => $titleMsg, 'body message' => $bodyMsg]);
    }

    public function reviewerPick($requisitionId) {
        $reviewRole = Role::where('name', RoleName::REVIEWER)->first();

        $reviewers = $reviewRole->users;

        return view('pages.reviewer.reviewerPick', ['reviewers' => $reviewers, 'requisitionId' => $requisitionId]);
    }

    public function reviews($requisitionId) {
        $req = Requisition::with('reviews')->find($requisitionId);
        
        return view('pages.reviewer.reviews', ['requisitionId' => $requisitionId, 'reviews' => $req->reviews]);
    }
}
