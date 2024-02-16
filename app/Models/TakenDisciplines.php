<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class TakenDisciplines extends Model
{
    use HasFactory;

    public function requisition() {
        return $this->belongsTo(Requisition::class);
    }
}
