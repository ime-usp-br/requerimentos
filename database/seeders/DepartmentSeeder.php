<?php

namespace Database\Seeders;

use App\Models\Department;
use Illuminate\Database\Seeder;
use App\Enums\DepartmentId;
use App\Enums\DepartmentName;

class DepartmentSeeder extends Seeder
{
    public function run()
    {
        $departments = [
            ['id' => DepartmentId::MAC, 'code' => "MAC", 'name' => DepartmentName::MAC],
            ['id' => DepartmentId::MAE, 'code' => "MAE", 'name' => DepartmentName::MAE],
            ['id' => DepartmentId::MAP, 'code' => "MAP", 'name' => DepartmentName::MAP],
            ['id' => DepartmentId::MAT, 'code' => "MAT", 'name' => DepartmentName::MAT],
            ['id' => DepartmentId::VRT, 'code' => 'VRT', 'name' => DepartmentName::EXTERNAL],
        ];

        foreach ($departments as $department) {
            Department::create($department);
        }
    }
}