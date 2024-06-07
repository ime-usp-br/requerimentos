<?php

namespace App\Http\Controllers;

use App\Models\Requisition;

class PreviousReviews extends Controller
{
    public function previousReviews($role, $requestedId) {
        $previousReviews = Requisition::where('requisitions.requested_disc_code', $requestedId)
                                    ->select(
                                        'requisitions.id',
                                        'taken_disciplines.code AS taken_codes',
                                        'taken_disciplines.year AS year_taken',
                                        'taken_disciplines.semester AS semester_taken',
                                        'taken_disciplines.institution',
                                        'requisitions.result', 
                                        'requisitions.updated_at AS result_date',
                                        'requisitions.result_text AS result_text'
                                    )
                                ->join('taken_disciplines', 'requisitions.id', '=', 'taken_disciplines.requisition_id') //inner join
                                ->join('reviews', 'requisitions.id', '=', 'reviews.requisition_id') //inner join
                                ->get()
                                ->groupBy('id');
        
        $previousReviewsFiltered = $previousReviews->filter(function ($group){
            return $group->contains(function ($object){
                return $object->institution === request()->institution;
            });
        });

        return view('pages.geral.previousReviews', ['requisitions' => $previousReviewsFiltered, 'role' => $role]);
    }
}
