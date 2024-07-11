<?php

namespace App\Http\Controllers;

use ReflectionClass;
use App\Enums\RoleId;
use App\Models\Event;
use App\Models\Review;
use App\Models\Document;
use App\Enums\DocumentType;
use App\Models\Requisition;
use Illuminate\Support\Facades\DB;
use App\Models\RequisitionsVersion;
use Illuminate\Support\Facades\Auth;
use App\Models\TakenDisciplinesVersion;

class RecordController extends Controller
{
    public function requisitionRecord($requisitionId) {

        $selectedColumns = ['created_at', 
                            'type', 
                            'author_name', 
                            'author_nusp', 
                            'id',
                            'message'];

        $events = Event::where('requisition_id', $requisitionId)
                        ->select($selectedColumns)
                        ->get();

        $roleToPreviousRouteMappings = 
        [ RoleId::REVIEWER => route('reviewer.show', ['requisitionId' => $requisitionId]),
          RoleId::SG => route('sg.show', ['requisitionId' => $requisitionId]),
          RoleId::MAC_SECRETARY => route('department.show', ['departmentName' => 'mac', 'requisitionId' => $requisitionId]),
          RoleId::MAE_SECRETARY => route('department.show', ['departmentName' => 'mae', 'requisitionId' => $requisitionId]),
          RoleId::MAT_SECRETARY => route('department.show', ['departmentName' => 'mat', 'requisitionId' => $requisitionId]),
          RoleId::MAP_SECRETARY => route('department.show', ['departmentName' => 'mac', 'requisitionId' => $requisitionId])
        ];

        $previousRoute = $roleToPreviousRouteMappings[Auth::user()->current_role_id];

        return view('pages.records.requisitionRecord', ['events' => $events, 'previousRoute' => $previousRoute]);
    }

    public function requisitionVersion($eventId) {

        $event = Event::find($eventId);
        $requisitionId = $event->requisition_id;

        $requisitionVersion = Requisition::with('takenDisciplines')->find($requisitionId);
        $takenDisciplines = $requisitionVersion->takenDisciplines;

        if ($requisitionVersion->latest_version !== $event->version) {

            $requisitionVersion = RequisitionsVersion
                            ::where('requisition_id', $requisitionId)
                            ->where('version', $event->version)
                            ->first();
            
            $takenDisciplines = TakenDisciplinesVersion
                            ::where('requisition_id', $requisitionId)
                            ->where('version', $event->version)
                            ->get();
        } 

        $requisitionVersionDocuments = Document
                                    ::where('created_at', 
                                            '<=', 
                                            $event->created_at)
                                    ->where('requisition_id', $requisitionId)
                                    ->orderBy('created_at', 'desc')
                                    ->get(); 

        

        $takenDiscsRecords = [];
        $currentCourseRecords = [];
        $takenDiscSyllabi = [];
        $requestedDiscSyllabi = [];

        foreach ($requisitionVersionDocuments as $document) {
            
            switch ($document->type) {
                case DocumentType::TAKEN_DISCS_RECORD:
                    array_push($takenDiscsRecords, $document);
                    break;
                case DocumentType::CURRENT_COURSE_RECORD:
                    array_push($currentCourseRecords, $document);
                    break;
                case DocumentType::TAKEN_DISCS_SYLLABUS:
                    array_push($takenDiscSyllabi, $document);
                    break;
                case DocumentType::REQUESTED_DISC_SYLLABUS:
                    array_push($requestedDiscSyllabi, $document);
                    break;
            }
        }

        return view('pages.records.requisitionVersion', 
                    ['req' => $requisitionVersion, 
                    'event' => $event,
                    'takenDiscs' => $takenDisciplines, 
                    'takenDiscsRecords' => $takenDiscsRecords, 
                    'currentCourseRecords' => $currentCourseRecords, 
                    'takenDiscSyllabi' => $takenDiscSyllabi, 
                    'requestedDiscSyllabi' => $requestedDiscSyllabi]);
        
    }
}
