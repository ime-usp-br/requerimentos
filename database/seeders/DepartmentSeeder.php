<?php

namespace Database\Seeders;

use App\Models\Department;
use Illuminate\Database\Seeder;

class DepartmentSeeder extends Seeder
{
    public function run()
    {
        $departments = [
            ['code' => 'MAC', 'name' => 'Ciência da Computação'],
            ['code' => 'MAE', 'name' => 'Estatística'],
            ['code' => 'MAP', 'name' => 'Matemática Aplicada'],
            ['code' => 'MAT', 'name' => 'Matemática'],
            ['code' => 'VRT', 'name' => 'Virtual'],
        ];

        foreach ($departments as $department) {
            Department::create($department);
        }
    }
}