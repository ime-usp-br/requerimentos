<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class Requisition extends Model
{
    use HasFactory;
    
    public function takenDisciplines() {
        return $this->hasMany(TakenDisciplines::class);
    }
}
