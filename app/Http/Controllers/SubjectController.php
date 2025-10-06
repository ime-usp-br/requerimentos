<?php

namespace App\Http\Controllers;

use App\Http\Controllers\Controller;
use App\Models\Replicado\ReplicadoSubject;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Log;

class SubjectController extends Controller
{
    /**
     * Search for subjects based on query parameter (searches both code and name)
     * Used during the initial requisition creation form
     */
    public function search(Request $request)
    {
        $query = $request->get('q', '');

        Log::debug('SubjectController::search - Subject search requested', [
            'query' => $query,
            'query_length' => strlen($query)
        ]);

        // Return empty array if query is too short
        if (strlen($query) < 2) {
            Log::debug('SubjectController::search - Query too short, returning empty results');
            return response()->json([]);
        }

        try {
            $subjects = ReplicadoSubject::where(function ($q) use ($query) {
                $q->whereRaw('LOWER(code) LIKE ?', ['%' . strtolower($query) . '%'])
                    ->orWhereRaw('LOWER(name) LIKE ?', ['%' . strtolower($query) . '%']);
            })
                ->orderBy('code')
                ->limit(10)
                ->get()
                ->map(function ($subject) {
                    return [
                        'code' => $subject->code,
                        'name' => $subject->name,
                        'label' => "{$subject->code} - {$subject->name}"
                    ];
                });

            Log::info('SubjectController::search - Search completed successfully', [
                'query' => $query,
                'results_count' => count($subjects)
            ]);

            return response()->json($subjects);
        } catch (\Exception $e) {
            Log::error('SubjectController::search - Search failed', [
                'query' => $query,
                'error' => $e->getMessage()
            ]);

            return response()->json(['error' => 'Failed to fetch subjects'], 500);
        }
    }
}
