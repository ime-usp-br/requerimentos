<?php

namespace App\Http\Controllers;

use App\Models\User;
use App\Models\Event;
use App\Models\Review;
use App\Models\ReviewsVersion;
use App\Models\DepartmentUserRole;
use App\Enums\RoleId;
use App\Enums\EventType;
use App\Models\Requisition;
use App\Notifications\ReviewerNotification;
use App\Notifications\ReviewGivenNotification;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\Validator;
use App\Models\Department;
use Inertia\Inertia;

class ReviewController extends Controller
{
    public function reviewerPick($requisitionId)
    {
        $requisitionDepartment = Requisition::find($requisitionId)->department;
        $departmentId = Department::where('name', $requisitionDepartment)->first()->id;
        $reviewers = DepartmentUserRole::getUsersWithRoleAndDepartment(RoleId::REVIEWER, $departmentId);

        return $reviewers;
    }

    public function createReview(Request $request)
    {
        $reviewerNusps = array_keys($request['reviewerNusps']);
        $requisitionId = $request['requisitionId'];
        // return $request;
        DB::transaction(function () use ($reviewerNusps, $requisitionId) {
            foreach ($reviewerNusps as $reviewerNusp) {
                // primeiro realiza a busca dos dados do parecerista
                $reviewer = User::where('codpes', $reviewerNusp)->first();

                $rev = Review::where('requisition_id', $requisitionId)
                    ->where('reviewer_nusp', $reviewer->codpes)
                    ->first();

                if (is_null($rev)) {
                    // Se a Review não existia, cria uma nova
                    $rev = new Review;
                    $rev->reviewer_name = $reviewer->name;
                    $rev->reviewer_nusp = $reviewer->codpes;
                    $rev->requisition_id = $requisitionId;
                    $rev->reviewer_decision = 'Sem decisão';
                    $rev->justification = null;
                    $rev->latest_version = 0;
                    $rev->save();
                }

                $user = Auth::user();

                $req = Requisition::find($requisitionId);

                // $req->situation é o que aparece na linha do requerimento na tabela
                // para o aluno (não contém o nome do parecerista)
                $req->situation = EventType::SENT_TO_REVIEWERS;

                $event = new Event;
                $event->type = EventType::SENT_TO_REVIEWERS;
                $event->requisition_id = $requisitionId;
                $event->author_name = $user->name;
                $event->author_nusp = $user->codpes;
                $event->version = $req->latest_version;

                if ($reviewer->name) {
                    // a $event->message/$req->internal_status contém o nome do
                    // parecerista, mas não aparece para o aluno, é usada apenas
                    // internamente
                    $event->message = "Enviado para o parecerista " . $reviewer->name;
                    $req->internal_status = $event->message;
                }

                $req->save();
                $event->save();

                $this->notifyReviewCreation($reviewerNusp);
            }
        });

        return response('', 200)->header('Content-Type', 'text/plain');
    }

    private function notifyReviewCreation($reviewer_nusp)
    {
        $reviewerUser = User::where('codpes', $reviewer_nusp)->first();

        if ($reviewerUser->email) {
            $reviewerUser->notify(new ReviewerNotification($reviewerUser));
        }
    }

    public function reviews($requisitionId)
    {
        $requisition = Requisition::with('reviews')->find($requisitionId);

        return Inertia::render('AssignedReviews', [
            'label' => 'Requerimentos',
            'selectedActions' => [],
            'reviews' => $requisition->reviews
        ]);
    }

    public function submit(Request $request)
    {
        // Validate the request using Laravel's validation system with translations
        $validator = Validator::make($request->all(), [
            'requisitionId' => 'required|exists:requisitions,id',
            'result' => 'required|string',
            'result_text' => 'required_if:result,Indeferido|nullable|string',
        ]);

        // If validation fails for any other reason
        if ($validator->fails()) {
            return back()->withErrors($validator);
        }

        $requisitionId = $request["requisitionId"];
        DB::transaction(function () use ($request, $requisitionId) {
            $user = Auth::user();

            $review = Review::where('requisition_id', $requisitionId)->where('reviewer_nusp', $user->codpes)->first();

            if ($review->latest_version > 0) {
                $new_hist = new ReviewsVersion;
                $new_hist->review_id = $review->id;
                $new_hist->reviewer_name = $user->name;
                $new_hist->reviewer_nusp = $user->codpes;
                $new_hist->requisition_id = $review->requisition_id;
                $new_hist->reviewer_decision = $review->reviewer_decision;
                $new_hist->justification = $review->justification;
                $new_hist->version = $review->latest_version;
                $new_hist->save();
            }

            $review->reviewer_decision = $request->result;
            $review->justification = $request->result_text;
            $review->latest_version = ($review->latest_version + 1);
            $review->save();

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

            // Delete all other empty reviews for this requisition
            $this->deleteEmptyReviews($requisitionId, $user->codpes);

            $this->notifyReviewGiven($requisitionId);
        });
    }

    private function notifyReviewGiven($requisitionId)
    {
        $requisitionDepartment = Requisition::find($requisitionId)->department;
        $departmentId = Department::where('name', $requisitionDepartment)->first()->id;
        $departmentUsers = DepartmentUserRole::getUsersWithRoleAndDepartment(RoleId::SECRETARY, $departmentId);

        foreach ($departmentUsers as $departmentUser) {
            if ($departmentUser->email)
                $departmentUser->notify(new ReviewGivenNotification($requisitionDepartment));
        }
    }

    /**
     * Delete all empty reviews for a requisition except the current user's review.
     * An empty review is one with reviewer_decision = 'Sem decisão', latest_version = 0, and justification is null or empty.
     */
    private function deleteEmptyReviews($requisitionId, $currentReviewerNusp)
    {
        Review::where('requisition_id', $requisitionId)
            ->where('reviewer_nusp', '!=', $currentReviewerNusp)
            ->where('reviewer_decision', 'Sem decisão')
            ->where(function ($query) {
                $query->whereNull('justification')->orWhere('justification', '');
            })
            ->delete();
    }
}
