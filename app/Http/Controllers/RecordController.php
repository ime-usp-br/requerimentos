<?php

namespace App\Http\Controllers;

use App\Models\Event;

class RecordController extends Controller
{
    
    public function requisitionRecord() {

        $selectedColumns = ['created_at', 
                            'type', 
                            'author_name', 
                            'author_nusp', 
                            'id'];

        $events = Event::select($selectedColumns)->get();

        return view('pages.records.requisitionRecord', ['events' => $events]);
    }

    public function requisitionVersion($eventId) {

        echo('ola');
        // $event = Event::find($requisitionId);
        
        // $documents = $req->documents->sortByDesc('created_at');

        // $takenDiscsRecords = [];
        // $currentCourseRecords = [];
        // $takenDiscSyllabi = [];
        // $requestedDiscSyllabi = [];

        // foreach ($documents as $document) {
        //     switch ($document->type) {
        //         case DocumentType::TAKEN_DISCS_RECORD:
        //             array_push($takenDiscsRecords, $document);
        //             break;
        //         case DocumentType::CURRENT_COURSE_RECORD:
        //             array_push($currentCourseRecords, $document);
        //             break;
        //         case DocumentType::TAKEN_DISCS_SYLLABUS:
        //             array_push($takenDiscSyllabi, $document);
        //             break;
        //         case DocumentType::REQUESTED_DISC_SYLLABUS:
        //             array_push($requestedDiscSyllabi, $document);
        //             break;
        //     }
        // }

        // return view('pages.department.detail', ['req' => $req, 'takenDiscs' => $req->takenDisciplines, 'takenDiscsRecords' => $takenDiscsRecords, 'currentCourseRecords' => $currentCourseRecords, 'takenDiscSyllabi' => $takenDiscSyllabi, 'requestedDiscSyllabi' => $requestedDiscSyllabi, 'departmentName' => $departmentName]);
    }
}
