<?php

namespace App\Http\Controllers;

use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\Storage;
use Illuminate\Support\Facades\Log;
use App\Enums\RoleId;
use App\Models\Document;

class DocumentsController extends Controller
{
    public function view($id)
    {
        Log::info('DocumentsController::view - Document view requested', [
            'document_id' => $id,
            'caller_user_codpes' => Auth::user()->codpes
        ]);

        $user = Auth::user();

        $document = Document::with('requisition')->find($id);

        if (!$document) {
            Log::warning('DocumentsController::view - Document not found', [
                'document_id' => $id,
                'user_codpes' => $user->codpes
            ]);
            abort(404);
        } else if ($user->current_role_id == RoleId::STUDENT && $user->codpes !== $document->requisition->student_nusp) {
            Log::warning('DocumentsController::view - Unauthorized document access attempt', [
                'document_id' => $id,
                'user_codpes' => $user->codpes,
                'document_owner' => $document->requisition->student_nusp
            ]);
            abort(403);
        }

        try {
            $filePath = Storage::disk('local')->path($document->path);

            Log::info('DocumentsController::view - Document served successfully', [
                'document_id' => $id,
                'user_codpes' => $user->codpes,
                'file_path' => $document->path
            ]);

            return response()->file($filePath, [
                'Content-Type' => 'application/pdf',
                'Content-Disposition' => 'inline; filename="Documento"'
            ]);
        } catch (\Exception $e) {
            Log::error('DocumentsController::view - Failed to serve document', [
                'document_id' => $id,
                'user_codpes' => $user->codpes,
                'error' => $e->getMessage()
            ]);

            abort(500, 'Failed to load document');
        }
    }
}
