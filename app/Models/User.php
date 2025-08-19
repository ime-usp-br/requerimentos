<?php

namespace App\Models;

// use Illuminate\Contracts\Auth\MustVerifyEmail;
use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Foundation\Auth\User as Authenticatable;
use Illuminate\Notifications\Notifiable;
use Illuminate\Support\Facades\Log;
use Spatie\Permission\Models\Role;
use App\Models\Department;
use App\Enums\RoleId;
use App\Models\DepartmentUserRole;

class User extends Authenticatable
{
    use \Spatie\Permission\Traits\HasRoles;
    use \Uspdev\SenhaunicaSocialite\Traits\HasSenhaunica;
    use HasFactory, Notifiable;

    /**
     * The attributes that are mass assignable.
     *
     * @var array<int, string>
     */
    protected $fillable = [
        'name',
        'email',
        'password',
        'current_role_id', // references roles.id
        'current_department_id', // nullable, references departments.id
        'codpes'
    ];

    /**
     * The attributes that should be hidden for serialization.
     *
     * @var list<string>
     */
    protected $hidden = [
        'password',
        'remember_token',
    ];

    /**
     * Get the attributes that should be cast.
     *
     * @return array<string, string>
     */
    protected function casts(): array
    {
        return [
            'email_verified_at' => 'datetime',
            'password' => 'hashed',
        ];
    }

    public function wrappedNotify($instance)
    {
        try {
            $this->notify($instance);
        } catch (\Exception $e) {
            Log::error('Notification failed: ' . $e->getMessage());
        }
    }

    /**
     * Get all department-role assignments for the user.
     */
    public function departmentUserRoles()
    {
        return $this->hasMany(DepartmentUserRole::class);
    }

    /**
     * Get the current role for the user.
     */
    public function currentRole()
    {
        return $this->belongsTo(Role::class, 'current_role_id');
    }

    /**
     * Get the current department for the user (nullable).
     */
    public function currentDepartment()
    {
        return $this->belongsTo(Department::class, 'current_department_id');
    }

    /**
     * Assign a role with an optional department to the user.
     */
    public function assignRole($roleId, $departmentId = null)
    {
        return DepartmentUserRole::firstOrCreate([
            'user_id' => $this->id,
            'role_id' => $roleId,
            'department_id' => $departmentId,
        ]);
    }
    private function findRole($roleId, $departmentId = null)
    {
        $query = DepartmentUserRole::where('user_id', $this->id)
            ->where('role_id', $roleId);

        if ($departmentId !== null) {
            $query->where('department_id', $departmentId);
        }

        return $query->first();
    }

    public function removeRole($roleId, $departmentId = null): void
    {
        $role = $this->findRole($roleId, $departmentId);

        if (!$role) {
            throw new \Exception('Role not found.');
        }

        $role->delete();
    }

    public function hasRole($roleId, $departmentId = null): bool
    {
        return $this->findRole($roleId, $departmentId) !== null;
    }

    public function changeCurrentRole($roleId, $departmentId = null)
    {
        if (!$this->hasRole($roleId, $departmentId)) {
            throw new \Exception('Unauthorized');
        }

        $this->current_role_id = $roleId;
        $this->current_department_id = $departmentId;
        $this->save();
    }

    /**
     * Get all roles for the user (with department info if needed).
     */
    public function getRolesAttribute()
    {
        return $this->departmentUserRoles()->with('role', 'department')->get();
    }
}
