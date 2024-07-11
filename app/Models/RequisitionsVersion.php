<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class RequisitionsVersion extends Model
{
    use HasFactory;

    protected $guarded = ['created_at', 'updated_at', 'id', 'version', 'requisition_id'];

    public function requisition() {
        return $this->belongsTo(Requisition::class);
    }
}
