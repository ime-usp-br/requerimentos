<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;

class Department extends Model
{
    protected $fillable = ['code', 'name'];

    public function users()
    {
        return $this->belongsToMany(User::class, 'user_departments');
    }
}