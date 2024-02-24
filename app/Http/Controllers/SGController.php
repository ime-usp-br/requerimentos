<?php

namespace App\Http\Controllers;

use Illuminate\Http\Request;
use App\Models\Requisition;

class SGController extends Controller
{
    public function list() {
        $selectedColumns = ['created_at', 'student_name', 'nusp', 'situation', 'department', 'id'];

        $reqs = Requisition::select($selectedColumns)->get();

        return view('pages.sg.list', ['reqs' => $reqs]);
    }
}
