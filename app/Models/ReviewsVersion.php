<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class ReviewsVersion extends Model
{
    use HasFactory;

    protected $guarded = [];

    public function review() {
        return $this->belongsTo(Review::class);
    }
}
