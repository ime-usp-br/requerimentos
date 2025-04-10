<?php

namespace App\Http\Controllers;

use App\Models\User;
use App\Models\Event;
use App\Enums\EventType;
use App\Models\Document;
use App\Enums\DocumentType;
use App\Models\Requisition;
use App\Models\TakenDisciplines;
use App\Models\RequisitionsVersion;
use App\Models\TakenDisciplinesVersion;
use App\Models\RequisitionsPeriod;
use Illuminate\Support\Facades\DB;
// use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;
use App\Http\Requests\RequisitionUpdateRequest;
use App\Http\Requests\RequisitionCreationRequest;
use App\Notifications\RequisitionResultNotification;

use App\Notifications\DepartmentNotification;
use Inertia\Inertia;

// use Illuminate\Support\Facades\Auth;
// use Illuminate\Support\Facades\Route;

class SGController extends Controller
{
    public function admin() {
        $selectedColumns = ['name', 'codpes', 'id'];
        $usersWithRoles = User::whereHas('roles')->select($selectedColumns)->get()
                          ->map(function ($user) {
                              return [
                                "id" => $user->id,
                                "codpes" => $user->codpes,
                                "name" => $user->name,
                                "roles" => $user->roles,
                              ];
                          });

        $requisition_period_status = RequisitionsPeriod::latest('id')->first();

        return Inertia::render('AdminPage', ['users' => $usersWithRoles, 'requisition_period_status' => $requisition_period_status->is_enabled]);
    }

    // public function requisition_period_toggle() {
    //     $currentStatus = RequisitionsPeriod::latest('id')->first();
        
    //     $newStatus = new RequisitionsPeriod;
    //     $newStatus->is_enabled = !$currentStatus->is_enabled;
    //     $newStatus->save();
        
    //     return redirect()->back()->with('success', ['info' => $newStatus->is_enabled]);
    // }
}
