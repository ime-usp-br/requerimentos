<?php

namespace App\Http\Controllers;

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
