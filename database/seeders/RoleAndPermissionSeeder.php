<?php

namespace Database\Seeders;

use App\Enums\RoleName;
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
        $roleNames = [RoleName::STUDENT,
                      RoleName::SG,
                      RoleName::REVIEWER,
                      RoleName::MAC_SECRETARY,
                      RoleName::MAE_SECRETARY,
                      RoleName::MAP_SECRETARY,
                      RoleName::MAT_SECRETARY, 
                      RoleName::VRT_SECRETARY];

        foreach ($roleNames as $roleName) {
            if (!Role::where('name', $roleName)->exists()) {
                Role::create(['name' => $roleName]);
            }
        } 

        Permission::create(['name' => 'admin']);
    }
}
