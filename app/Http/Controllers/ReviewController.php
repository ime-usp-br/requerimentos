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
use Illuminate\Http\Request;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Auth;
use App\Models\Department;
use Inertia\Inertia;

class ReviewController extends Controller
{
    public function reviewerPick($requisitionId)
    {   
        $requisitionDepartment = Requisition::find($requisitionId)->department;
        $departmentId = Department::where('code', $requisitionDepartment)->first()->id;
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

                $reviewerUser = User::where('codpes', $reviewer->codpes)->first();

                // se o parecerista nunca logou no sistema, o email dele é desconhecido 
                if ($reviewerUser->email && env('APP_ENV') === 'production') {
                    $reviewerUser->notify(new ReviewerNotification($reviewerUser));
                }
            }
        });

        return response('', 200)->header('Content-Type', 'text/plain');
    }

    public function reviews($requisitionId)
    {
        $requisition = Requisition::with('reviews')->find($requisitionId);

        return Inertia::render('AssignedReviews', [ 'label' => 'Requerimentos', 
                                                    'selectedActions' => [],
                                                    'reviews' => $requisition->reviews ]);
    }
    
    public function submit(Request $request)
    {
        $requisitionId = $request["requisitionId"];
        DB::transaction(function () use ($request, $requisitionId) {
            $user = Auth::user();

            $review = Review::where('requisition_id', $requisitionId)->where('reviewer_nusp', $user->codpes)->first();
            // Não tem devemos salvar a versão 0, já que ela
            // não tem informação dada pelo parecerista.
            if($review->latest_version > 0){
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
        });
    }

    // public function previousReviews($requisitionId, $requestedDiscCode)
    // {

    //     // O retorno da Query é um grupo, j́a que um mesmo requisito pode ter mais 
    //     // de uma matéria realizada. 
    //     $previousReviews = Requisition::where('requisitions.requested_disc_code', $requestedDiscCode)
    //         ->select(
    //             'requisitions.id',
    //             'taken_disciplines.code AS taken_codes',
    //             'taken_disciplines.year AS year_taken',
    //             'taken_disciplines.semester AS semester_taken',
    //             'reviews.reviewer_decision',
    //             'reviews.updated_at AS review_date',
    //             'reviews.justification AS reviewer_justification',
    //             'taken_disciplines.institution'
    //         )
    //         ->join('taken_disciplines', 'requisitions.id', '=', 'taken_disciplines.requisition_id') //inner join
    //         ->join('reviews', 'requisitions.id', '=', 'reviews.requisition_id') //inner join
    //         ->where('requisitions.id', '!=', $requisitionId) // Não queremos ver o parecer do requerimento atual
    //         ->get()
    //         ->groupBy('id');

    //     // Dentre todas as disciplinas que foram utilizadas para pedir cada aproveitamento,
    //     // verifica se alguma delas é da mesma instituição que o pedido atual.
    //     // Se não definimos nenhuma instituição na pesquisa, então exibimos todas.
    //     $previousReviewsFiltered = $previousReviews->filter(function ($group) {
    //         return $group->contains(function ($object) {
    //             return ($object->institution === request()->institution) or !(isset(request()->institution));
    //         });
    //     });

    //     return view('pages.geral.previousReviews', ['requisitionId' => $requisitionId, 'previousRequisitions' => $previousReviewsFiltered]);
    // }

    // public function copy($requisitionId, Request $request)
    // {
    //     $user = Auth::user();

    //     $reviewToBeUpdated = Review::where('requisition_id', $requisitionId)->where('reviewer_nusp', $user->codpes)->first();

    //     $reviewToBeUpdated->reviewer_decision = $request->decision;
    //     $reviewToBeUpdated->justification = $request->justification;

    //     $reviewToBeUpdated->save();

    //     $bodyMsg = "O parecer foi copiado para o requerimento atual. Lembre de enviar o parecer quando ele estiver concluído.<br/><br/> ID do requerimento: $requisitionId";
    //     $titleMsg = 'Parecer copiado';

    //     return redirect()->route('reviewer.show', ['requisitionId' => $requisitionId])
    //         ->with('success', ['title message' => $titleMsg, 'body message' => $bodyMsg, 'return button' => false])
    //         ->withFragment('decision'); #withFragment redireciona para o conteúdo com ID=decision
    // }

}