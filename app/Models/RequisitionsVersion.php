<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class RequisitionsVersion extends Model
{
    use HasFactory;

    protected $fillable = [
        'requisition_id',
        'version',
        'department',
        'student_nusp',
        'student_name',
        'email',
        'course',
        'requested_disc',
        'requested_disc_type',
        'requested_disc_code',
        'observations',
        'result',
        'result_text',
        'taken_disciplines_version',
        'taken_disc_record_version',
        'course_record_version',
        'taken_disc_syllabus_version',
        'requested_disc_syllabus_version'
    ];

    public function requisition() {
        return $this->belongsTo(Requisition::class);
    }
}
