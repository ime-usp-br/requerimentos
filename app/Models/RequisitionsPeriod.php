<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class RequisitionsPeriod extends Model
{
    use HasFactory;
    protected $fillable = ['is_enabled'];

    // Preventing updates by overriding the save method
    public function save(array $options = [])
    {
        if (!$this->exists) {
            return parent::save($options);
        }
        
        throw new \Exception("Updates are not allowed.");
    }
}
