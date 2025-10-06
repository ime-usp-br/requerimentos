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
use Illuminate\Support\Facades\Log;
use Illuminate\Support\Facades\Validator;
use App\Models\Department;
use Inertia\Inertia;

class ReviewController extends Controller
{
    public function reviewerPick($requisitionId)
    {
        Log::info('ReviewController::reviewerPick - Getting reviewers for requisition', [
            'requisition_id' => $requisitionId,
            'caller_user_codpes' => Auth::user()->codpes
        ]);

        $requisitionDepartment = Requisition::find($requisitionId)->department;
        $departmentId = Department::where('name', $requisitionDepartment)->first()->id;
        $reviewers = DepartmentUserRole::getUsersWithRoleAndDepartment(RoleId::REVIEWER, $departmentId);

        Log::info('ReviewController::reviewerPick - Reviewers retrieved', [
            'requisition_id' => $requisitionId,
            'department' => $requisitionDepartment,
            'reviewer_count' => count($reviewers)
        ]);

        return $reviewers;
    }

    public function createReview(Request $request)
    {
        $reviewerNusps = array_keys($request['reviewerNusps']);
        $requisitionId = $request['requisitionId'];

        Log::info('ReviewController::createReview - Creating reviews', [
            'requisition_id' => $requisitionId,
            'reviewer_count' => count($reviewerNusps),
            'caller_user_codpes' => Auth::user()->codpes
        ]);

        DB::transaction(function () use ($reviewerNusps, $requisitionId) {
            foreach ($reviewerNusps as $reviewerNusp) {
                Log::info('ReviewController::createReview - Processing reviewer', [
                    'reviewer_nusp' => $reviewerNusp,
                    'requisition_id' => $requisitionId
                ]);

                // primeiro realiza a busca dos dados do parecerista
                $reviewer = User::where('codpes', $reviewerNusp)->first();

                $rev = Review::where('requisition_id', $requisitionId)
                    ->where('reviewer_nusp', $reviewer->codpes)
                    ->first();

                if (is_null($rev)) {
                    Log::info('ReviewController::createReview - Creating new review', [
                        'reviewer_nusp' => $reviewerNusp,
                        'requisition_id' => $requisitionId
                    ]);

                    // Se a Review não existia, cria uma nova
                    $rev = new Review;
                    $rev->reviewer_name = $reviewer->name;
                    $rev->reviewer_nusp = $reviewer->codpes;
                    $rev->requisition_id = $requisitionId;
                    $rev->reviewer_decision = 'Sem decisão';
                    $rev->justification = null;
                    $rev->latest_version = 0;
                    $rev->save();
                } else {
                    Log::info('ReviewController::createReview - Review already exists', [
                        'reviewer_nusp' => $reviewerNusp,
                        'requisition_id' => $requisitionId
                    ]);
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

                Log::info('ReviewController::createReview - Review process completed for reviewer', [
                    'reviewer_nusp' => $reviewerNusp,
                    'requisition_id' => $requisitionId,
                    'event_id' => $event->id
                ]);

                $this->notifyReviewCreation($reviewerNusp);
            }
        });

        Log::info('ReviewController::createReview - All reviews created successfully', [
            'requisition_id' => $requisitionId,
            'total_reviewers' => count($reviewerNusps)
        ]);

        return response('', 200)->header('Content-Type', 'text/plain');
    }

    private function notifyReviewCreation($reviewer_nusp)
    {
        Log::info('ReviewController::notifyReviewCreation - Sending notification', [
            'reviewer_nusp' => $reviewer_nusp
        ]);

        $reviewerUser = User::where('codpes', $reviewer_nusp)->first();

        if ($reviewerUser->email) {
            $reviewerUser->notify(new ReviewerNotification($reviewerUser));

            Log::info('ReviewController::notifyReviewCreation - Notification sent successfully', [
                'reviewer_nusp' => $reviewer_nusp,
                'email' => $reviewerUser->email
            ]);
        } else {
            Log::warning('ReviewController::notifyReviewCreation - No email found for reviewer', [
                'reviewer_nusp' => $reviewer_nusp
            ]);
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
        Log::info('ReviewController::submit - Starting review submission', [
            'requisition_id' => $request->get('requisitionId'),
            'result' => $request->get('result'),
            'caller_user_codpes' => Auth::user()->codpes,
            'has_result_text' => !empty($request->get('result_text'))
        ]);

        // Validate the request using Laravel's validation system with translations
        $validator = Validator::make($request->all(), [
            'requisitionId' => 'required|exists:requisitions,id',
            'result' => 'required|string',
            'result_text' => 'required_if:result,Indeferido|nullable|string',
        ]);

        // If validation fails for any other reason
        if ($validator->fails()) {
            Log::warning('ReviewController::submit - Validation failed', [
                'requisition_id' => $request->get('requisitionId'),
                'caller_user_codpes' => Auth::user()->codpes,
                'errors' => $validator->errors()->toArray()
            ]);
            return back()->withErrors($validator);
        }

        $requisitionId = $request["requisitionId"];

        Log::info('ReviewController::submit - Starting database transaction', [
            'requisition_id' => $requisitionId,
            'caller_user_codpes' => Auth::user()->codpes
        ]);

        DB::transaction(function () use ($request, $requisitionId) {
            $user = Auth::user();

            Log::info('ReviewController::submit - Retrieving review for user', [
                'requisition_id' => $requisitionId,
                'reviewer_nusp' => $user->codpes,
                'caller_user_codpes' => Auth::user()->codpes
            ]);

            $review = Review::where('requisition_id', $requisitionId)->where('reviewer_nusp', $user->codpes)->first();

            if (!$review) {
                Log::error('ReviewController::submit - Review not found for user', [
                    'requisition_id' => $requisitionId,
                    'reviewer_nusp' => $user->codpes,
                    'caller_user_codpes' => Auth::user()->codpes
                ]);
                throw new \Exception('Review not found for current user');
            }

            Log::info('ReviewController::submit - Review found', [
                'review_id' => $review->id,
                'current_version' => $review->latest_version,
                'current_decision' => $review->reviewer_decision
            ]);

            if ($review->latest_version > 0) {
                Log::info('ReviewController::submit - Creating review version history', [
                    'review_id' => $review->id,
                    'version' => $review->latest_version
                ]);

                $new_hist = new ReviewsVersion;
                $new_hist->review_id = $review->id;
                $new_hist->reviewer_name = $user->name;
                $new_hist->reviewer_nusp = $user->codpes;
                $new_hist->requisition_id = $review->requisition_id;
                $new_hist->reviewer_decision = $review->reviewer_decision;
                $new_hist->justification = $review->justification;
                $new_hist->version = $review->latest_version;
                $new_hist->save();

                Log::info('ReviewController::submit - Review version history created', [
                    'review_version_id' => $new_hist->id,
                    'version' => $new_hist->version
                ]);
            }

            Log::info('ReviewController::submit - Updating review with new decision', [
                'review_id' => $review->id,
                'new_decision' => $request->result,
                'new_version' => $review->latest_version + 1,
                'has_justification' => !empty($request->result_text)
            ]);

            $review->reviewer_decision = $request->result;
            $review->justification = $request->result_text;
            $review->latest_version = ($review->latest_version + 1);
            $review->save();

            Log::info('ReviewController::submit - Review updated successfully', [
                'review_id' => $review->id,
                'new_version' => $review->latest_version
            ]);

            $req = Requisition::find($requisitionId);
            $req->situation = EventType::RETURNED_BY_REVIEWER;

            Log::info('ReviewController::submit - Creating event for review submission', [
                'requisition_id' => $requisitionId,
                'event_type' => EventType::RETURNED_BY_REVIEWER,
                'author_nusp' => $user->codpes,
                'requisition_version' => $req->latest_version
            ]);

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

            Log::info('ReviewController::submit - Event and requisition updated', [
                'event_id' => $event->id,
                'requisition_id' => $requisitionId,
                'new_situation' => $req->situation
            ]);

            // Delete all other empty reviews for this requisition
            Log::info('ReviewController::submit - Deleting empty reviews for other reviewers', [
                'requisition_id' => $requisitionId,
                'current_reviewer_nusp' => $user->codpes
            ]);

            $this->deleteEmptyReviews($requisitionId, $user->codpes);

            Log::info('ReviewController::submit - Sending review given notification', [
                'requisition_id' => $requisitionId
            ]);

            $this->notifyReviewGiven($requisitionId);

            Log::info('ReviewController::submit - Review submission completed successfully', [
                'requisition_id' => $requisitionId,
                'review_id' => $review->id,
                'final_version' => $review->latest_version,
                'decision' => $review->reviewer_decision,
                'user_codpes' => $user->codpes
            ]);
        });

        Log::info('ReviewController::submit - Database transaction completed successfully', [
            'requisition_id' => $requisitionId,
            'user_codpes' => Auth::user()->codpes
        ]);
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
