<?php

namespace Database\Seeders;

use App\Enums\RoleName;
use App\Enums\RoleId;
use Illuminate\Database\Seeder;
use Spatie\Permission\Models\Permission;
use Spatie\Permission\Models\Role;

class RoleAndPermissionSeeder extends Seeder
{
    /**
     * Run the database seeds.
     *
     * @return void
     */
    public function run()
    {   
        $roles = [
            RoleId::STUDENT => RoleName::STUDENT,
            RoleId::SG => RoleName::SG,
            RoleId::REVIEWER => RoleName::REVIEWER,
            RoleId::MAC_SECRETARY => RoleName::MAC_SECRETARY,
            RoleId::MAE_SECRETARY => RoleName::MAE_SECRETARY,
            RoleId::MAP_SECRETARY => RoleName::MAP_SECRETARY,
            RoleId::MAT_SECRETARY => RoleName::MAT_SECRETARY,
            RoleId::VRT_SECRETARY => RoleName::VRT_SECRETARY,
        ];

        foreach ($roles as $roleId => $roleName) {
            if (!Role::where('id', $roleId)->exists()) {
                Role::create(['id' => $roleId, 'name' => $roleName]);
            }
        }

        Permission::create(['name' => 'admin']);
    }
}
