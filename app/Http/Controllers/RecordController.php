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
                            'id'];

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

        echo('ola');
    }
}
