<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class Event extends Model
{
    use HasFactory;
    
    protected $fillable = [
        'requisition_id',
        'type',
        'version',
        'author_name',
        'author_nusp',
        'message'
    ];

    public function requisition() {
        return $this->belongsTo(Requisition::class);
    }
}
