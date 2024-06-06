<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class TakenDisciplinesVersion extends Model
{
    use HasFactory;

    protected $guarded = ['created_at', 'updated_at', 'id', 'version'];

    public function requisition() {
        return $this->belongsTo(Requisition::class);
    }
}
