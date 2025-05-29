<?php

namespace App\Http\Controllers;

use ReflectionClass;
use App\Enums\RoleId;
use App\Models\Event;
use App\Models\Review;
use App\Models\Document;
use App\Enums\DocumentType;
use App\Models\Requisition;
use App\Models\RequisitionsVersion;
use App\Models\TakenDisciplines;
use Illuminate\Support\Facades\Auth;
use Inertia\Inertia;

class RecordController extends Controller
{
    public function requisitionRecord($requisitionId) {
        $user = Auth::user();
        $roleId = $user->current_role_id;

        $selectedEventColumns = ['created_at', 
                                 'type', 
                                 'author_name', 
                                 'author_nusp', 
                                 'id',
                                 'message'];

        $events = Event::where('requisition_id', $requisitionId)
                        ->select($selectedEventColumns)
                        ->get();

        $selectedColumns = ['type', 'created_at', 'ocurrence_time', 'author_name', 'author_nusp'];

        return Inertia::render('RequisitionEventHistoryPage', ['label' => 'Histórico do Requerimento ' . $requisitionId,
                                                   'events' => $events, 
                                                   'selectedColumns' => $selectedColumns,
                                                   'selectedActions' => [],
                                                   'roleId' => $roleId, 
                                                   'userRoles' => $user->roles,
                                                   'requisitionId' => $requisitionId]);
    }

    public function requisitionVersion($eventId) {

        $event = Event::find($eventId);
        $requisitionId = $event->requisition_id;

        $requisition = Requisition::with('takenDisciplines')->find($requisitionId);
        $documents = [];

        if ((int)$requisition->latest_version !== (int)$event->version) {
            $requisitionVersion = RequisitionsVersion::where('requisition_id', $requisitionId)
                                                    ->where('version', $event->version)
                                                    ->first();
            
            if (!$requisitionVersion) {
                abort(404, 'Historical version not found');
            }
            
            $takenDisciplines = TakenDisciplines::where('requisition_id', $requisitionId)
                                                    ->where('version', $requisitionVersion->taken_disciplines_version)
                                                    ->get();

            $documentQueries = [
                DocumentType::TAKEN_DISCS_RECORD => $requisitionVersion->taken_disc_record_version,
                DocumentType::CURRENT_COURSE_RECORD => $requisitionVersion->course_record_version,
                DocumentType::TAKEN_DISCS_SYLLABUS => $requisitionVersion->taken_disc_syllabus_version,
                DocumentType::REQUESTED_DISC_SYLLABUS => $requisitionVersion->requested_disc_syllabus_version,
            ];

            foreach ($documentQueries as $documentType => $documentVersion) {
                $document = Document::where('requisition_id', $requisitionId)
                                  ->where('type', $documentType)
                                  ->where('version', $documentVersion)
                                  ->first();
                if ($document) {
                    $documents[] = $document;
                }
            }
            
            $requisitionData = $requisitionVersion->toArray();
            $requisitionData['id'] = $requisitionId;
            
        } else {
            $latestTakenDisciplinesVersion = TakenDisciplines::where('requisition_id', $requisitionId)
                                                            ->max('version');
            
            $takenDisciplines = TakenDisciplines::where('requisition_id', $requisitionId)
                                                ->where('version', $latestTakenDisciplinesVersion)
                                                ->get();
            
            $documentTypes = [
                DocumentType::TAKEN_DISCS_RECORD,
                DocumentType::CURRENT_COURSE_RECORD,
                DocumentType::TAKEN_DISCS_SYLLABUS,
                DocumentType::REQUESTED_DISC_SYLLABUS
            ];
            
            $documents = [];
            foreach ($documentTypes as $documentType) {
                $document = Document::where('requisition_id', $requisitionId)
                                  ->where('type', $documentType)
                                  ->orderBy('version', 'desc')
                                  ->first();
                if ($document) {
                    $documents[] = $document;
                }
            }
            
            $requisitionData = $requisition->toArray();
        }

        return Inertia::render('RequisitionVersionDetailPage', [
            'requisition' => $requisitionData,
            'event' => $event,
            'takenDisciplines' => $takenDisciplines,
            'documents' => $documents,
            'label' => 'Versão ' . $event->version . ' do requerimento ' . $requisitionId,
            'requisitionId' => $requisitionId,
        ]);
    }
}
