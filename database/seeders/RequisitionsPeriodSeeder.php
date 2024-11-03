<?php

namespace Database\Seeders;

use Illuminate\Database\Seeder;
use App\Models\RequisitionsPeriod;

class RequisitionsPeriodSeeder extends Seeder
{
    /**
     * Run the database seeds.
     *
     * @return void
     */
    public function run()
    {
        RequisitionsPeriod::Create(
            ['is_enabled' => true]
        );
    }
}
