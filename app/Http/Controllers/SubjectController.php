<?php

namespace App\Http\Controllers;

use App\Http\Controllers\Controller;
use App\Models\Replicado\ReplicadoSubject;
use Illuminate\Http\Request;

class SubjectController extends Controller
{
    /**
     * Search for subjects based on query parameter (searches both code and name)
     */
    public function search(Request $request)
    {
        $query = $request->get('q', '');

        // Return empty array if query is too short
        if (strlen($query) < 2) {
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

            return response()->json($subjects);
        } catch (\Exception $e) {
            return response()->json(['error' => 'Failed to fetch subjects'], 500);
        }
    }
}
