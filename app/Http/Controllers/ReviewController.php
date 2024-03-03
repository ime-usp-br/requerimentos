<?php

namespace App\Http\Controllers;

use Illuminate\Http\Request;
use App\Models\Review;

class ReviewController extends Controller
{
    public function createReview($requisitionId, Request $request) {
        
        Review::firstOrCreate(['reviewer_nusp' => $request->nusp, 'requisition_id' => $requisitionId], ['reviewer_decision' => 'Sem decisão', 'requisition_id' => $requisitionId, 'justification' => null, 'reviewer_nusp' => $request->nusp, 'reviewer_name' => $request->name]);
        // $review = new Review;
        // $review->reviewer_decision = 'Sem decisão';
        // $review->requisition_id = $requisitionId;
        // $review->justification = null;
        // $review->reviewer_nusp = $request->nusp;
        // $review->reviewer_name = $request->name;
        // $review->save();

        return response()->noContent();
    }
}
