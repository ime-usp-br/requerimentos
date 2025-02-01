<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class Requisition extends Model
{
    use HasFactory;

    protected $guarded = ['created_at', 'updated_at', 'id', 'latest_version', 'situation', 'internal_status', 'validated'];
    
    public function takenDisciplines() {
        return $this->hasMany(TakenDisciplines::class);
    }

    public function reviews() {
        return $this->hasMany(Review::class);
    }

    public function requisitionsVersions() {
        return $this->hasMany(RequisitionsVersion::class);
    }

    public function events() {
        return $this->hasMany(Event::class);
    }

    public function documents() {
        return $this->hasMany(Document::class);
    }
}
