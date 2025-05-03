<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;

class DepartmentUserRole extends Model
{
    protected $table = 'department_user_roles';
    protected $fillable = [
        'user_id',
        'department_id',
        'role_id',
    ];

    public function user()
    {
        return $this->belongsTo(User::class);
    }

    public function department()
    {
        return $this->belongsTo(Department::class);
    }

    public function role()
    {
        return $this->belongsTo(\Spatie\Permission\Models\Role::class);
    }
}
