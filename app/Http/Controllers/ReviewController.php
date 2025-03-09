<?php

namespace App\Http\Controllers;

use App\Models\User;
use App\Models\Event;
use App\Models\Review;
use App\Models\ReviewsVersion;
use App\Enums\RoleName;
use App\Enums\EventType;
use App\Enums\DocumentType;
use App\Models\Requisition;
use App\Notifications\ReviewerNotification;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\DB;
use Spatie\Permission\Models\Role;
use Illuminate\Support\Facades\Auth;
use Inertia\Inertia;

class ReviewController extends Controller
{
    // public function list()
    // {
    //     $user = Auth::user();
    //     $roleId = $user->current_role_id;

    //     $selectedReviewColumns = ['requisitions.created_at', 'student_name', 'requested_disc', 'reviewer_decision', 'reviews.updated_at', 'requisitions.id'];

    //     $requisitions = DB::table('reviews')
    //         ->join('requisitions', 'reviews.requisition_id', '=', 'requisitions.id')
    //         ->where('reviewer_nusp', $user->codpes)
    //         ->where('requisitions.situation', '=', 'Enviado para análise dos pareceristas')
    //         ->select($selectedReviewColumns)->get();

    //     $selectedColumns = ['created_at', 'student_name', 'requested_disc', 'reviewer_decision', 'updated_at', 'id'];
    //     return Inertia::render('RequisitionList', ['requisitions' => $requisitions, 
    //                                                'selectedColumns' => $selectedColumns,
    //                                                'roleId' => $roleId, 
    //                                                'userRoles' => $user->roles]);
    // }

    // public function show($requisitionId)
    // {

    //     $user = Auth::user();

    //     $req = Requisition::with('takenDisciplines', 'documents')->find($requisitionId);

    //     $review = Review::where('reviewer_nusp', $user->codpes)
    //         ->where('requisition_id', $requisitionId)
    //         ->first();

    //     $documents = $req->documents->sortByDesc('created_at');

    //     $takenDiscsRecords = [];
    //     $currentCourseRecords = [];
    //     $takenDiscSyllabi = [];
    //     $requestedDiscSyllabi = [];

    //     foreach ($documents as $document) {
    //         switch ($document->type) {
    //             case DocumentType::TAKEN_DISCS_RECORD:
    //                 array_push($takenDiscsRecords, $document);
    //                 break;
    //             case DocumentType::CURRENT_COURSE_RECORD:
    //                 array_push($currentCourseRecords, $document);
    //                 break;
    //             case DocumentType::TAKEN_DISCS_SYLLABUS:
    //                 array_push($takenDiscSyllabi, $document);
    //                 break;
    //             case DocumentType::REQUESTED_DISC_SYLLABUS:
    //                 array_push($requestedDiscSyllabi, $document);
    //                 break;
    //         }
    //     }

    //     return view('pages.reviewer.detail', ['req' => $req, 'takenDiscs' => $req->takenDisciplines, 'takenDiscsRecords' => $takenDiscsRecords, 'currentCourseRecords' => $currentCourseRecords, 'takenDiscSyllabi' => $takenDiscSyllabi, 'requestedDiscSyllabi' => $requestedDiscSyllabi, 'review' => $review]);
    // }

    // public function createReview($requisitionId, Request $request)
    // {

    //     DB::transaction(function () use ($request, $requisitionId) {
    //         $rev = Review::where('requisition_id', $requisitionId)
    //             ->where('reviewer_nusp', $request->codpes)
    //             ->first();

    //         if (is_null($rev)) {
    //             // Se a Review não existia, cria uma nova
    //             $rev = new Review;
    //             $rev->reviewer_name = $request->name;
    //             $rev->reviewer_nusp = $request->codpes;
    //             $rev->requisition_id = $requisitionId;
    //             $rev->reviewer_decision = 'Sem decisão';
    //             $rev->justification = null;
    //             $rev->latest_version = 0;
    //             $rev->save();
    //         }

    //         $user = Auth::user();

    //         $req = Requisition::find($requisitionId);

    //         // $req->situation é o que aparece na linha do requerimento na tabela 
    //         // para o aluno (não contém o nome do parecerista)
    //         $req->situation = EventType::SENT_TO_REVIEWERS;
    //         $req->validated = true;

    //         $event = new Event;
    //         $event->type = EventType::SENT_TO_REVIEWERS;
    //         $event->requisition_id = $requisitionId;
    //         $event->author_name = $user->name;
    //         $event->author_nusp = $user->codpes;
    //         $event->version = $req->latest_version;

    //         if ($request->name) {
    //             // a $event->message/$req->internal_status contém o nome do  
    //             // parecerista, mas não aparece para o aluno, é usada apenas  
    //             // internamente
    //             $event->message = "Enviado para o parecerista " . $request->name;
    //             $req->internal_status = $event->message;
    //         }

    //         $req->save();
    //         $event->save();

    //         $reviewerUser = User::where('codpes', $request->codpes)->first();

    //         // se o parecerista nunca logou no sistema, o email dele é desconhecido 
    //         if ($reviewerUser->email && env('APP_ENV') === 'production') {
    //             $reviewerUser->notify(new ReviewerNotification($reviewerUser));
    //         }
    //     });

    //     return response()->noContent();
    // }

    // public function saveOrSubmit($requisitionId, Request $request)
    // {
    //     $action = $request->input('action');
    //     if ($action === "save") {
    //         $this->save($requisitionId, $request);

    //         $bodyMsg = 'As informações do parecer foram salvas';
    //         $titleMsg = 'Parecer salvo';
    //         return redirect()->route('reviewer.show', ['requisitionId' => $requisitionId])
    //             ->with('success', ['title message' => $titleMsg, 'body message' => $bodyMsg, 'return button' => false])
    //             ->withFragment("decision");
    //     } else if ($action === "submit") {
    //         $this->submit($requisitionId, $request);

    //         $bodyMsg = 'As informações do parecer foram enviadas';
    //         $titleMsg = 'Parecer enviado';

    //         return redirect()->route('reviewer.list', ['requisitionId' => $requisitionId])->with('success', ['title message' => $titleMsg, 'body message' => $bodyMsg]);
    //     }
    // }

    // private function save($requisitionId, Request $request)
    // {
    //     $user = Auth::user();

    //     $reviewToBeSaved = Review::where('requisition_id', $requisitionId)
    //         ->where('reviewer_nusp', $user->codpes)
    //         ->first();

    //     $reviewToBeSaved->reviewer_decision = $request->decision;
    //     $reviewToBeSaved->justification = $request->justification;
    //     $reviewToBeSaved->save();
    // }

    // private function submit($requisitionId, Request $request)
    // {
    //     DB::transaction(function () use ($request, $requisitionId) {

    //         $user = Auth::user();

    //         $review = Review::where('requisition_id', $requisitionId)->where('reviewer_nusp', $user->codpes)->first();
            
    //         // Não tem devemos salvar a versão 0, já que ela
    //         // não tem informação dada pelo parecerista.
    //         if($review->latest_version > 0){
    //             $new_hist = new ReviewsVersion;
    //             $new_hist->review_id = $review->id;
    //             $new_hist->reviewer_name = $user->name;
    //             $new_hist->reviewer_nusp = $user->codpes;
    //             $new_hist->requisition_id = $review->requisition_id;
    //             $new_hist->reviewer_decision = $review->reviewer_decision;
    //             $new_hist->justification = $review->justification;
    //             $new_hist->version = $review->latest_version;
    //             $new_hist->save();
    //         }

    //         $review->reviewer_decision = $request->decision;
    //         $review->justification = $request->justification;
    //         $review->latest_version = ($review->latest_version + 1);
    //         $review->save();


    //         $req = Requisition::find($requisitionId);
    //         $req->situation = EventType::RETURNED_BY_REVIEWER;

    //         $event = new Event;
    //         $event->type = EventType::RETURNED_BY_REVIEWER;
    //         $event->requisition_id = $requisitionId;
    //         $event->author_name = $user->name;
    //         $event->author_nusp = $user->codpes;
    //         $event->version = $req->latest_version;

    //         $event->message = "Retornado pelo parecerista " . $user->name;
    //         $req->internal_status = $event->message;

    //         $event->save();
    //         $req->save();
    //     });
    // }

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

    // public function reviewerPick($requisitionId)
    // {
    //     $reviewRole = Role::where('name', RoleName::REVIEWER)->first();

    //     $reviewers = $reviewRole->users;

    //     return view('pages.reviewer.reviewerPick', ['reviewers' => $reviewers, 'requisitionId' => $requisitionId]);
    // }

    // public function reviews($requisitionId)
    // {
    //     $req = Requisition::with('reviews')->find($requisitionId);

    //     return view('pages.reviewer.reviews', ['requisitionId' => $requisitionId, 'reviews' => $req->reviews]);
    // }
}
