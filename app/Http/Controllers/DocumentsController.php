<?php

namespace App\Http\Controllers;

use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\Storage;
use App\Enums\RoleId;
use App\Models\Document;

class DocumentsController extends Controller
{
    public function view($id)
    {
        $user = Auth::user();

        $document = Document::with('requisition')->find($id);

        if (!$document) {
            abort(404);
        } 
		else if($user->current_role_id == RoleId::STUDENT && $user->codpes !== $document->requisition->student_nusp){
			abort(403);
		}

        $filePath = Storage::disk('local')->path($document->path);
        return response()->file($filePath, ['Content-Type' => 'application/pdf',
											'Content-Disposition' => 'inline; filename="Documento"']);
    }
}
