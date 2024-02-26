<?php

namespace Database\Seeders;

use App\Enums\RoleName;
use Illuminate\Database\Seeder;
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

        // $roles = ['Aluno', 'Secretaria de Graduação', 'Parecerista', 'Coordenador do MAC', 'Coordenador do MAE', 'Coordenador do MAP', 'Coordenador do MAT'];

        $roles = [RoleName::STUDENT, RoleName::SG, RoleName::REVIEWER, RoleName::MAC_COORD, RoleName::MAE_COORD, RoleName::MAP_COORD, RoleName::MAT_COORD];

        foreach ($roles as $roleName) {
            if (!Role::where('name', $roleName)->exists()) {
                Role::create(['name' => $roleName]);
            }
        } 
    }
}
