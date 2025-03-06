<?php

namespace Database\Seeders;

use Illuminate\Database\Console\Seeds\WithoutModelEvents;
use Illuminate\Database\Seeder;
use Illuminate\Support\Facades\DB;

class InspectionTypesTableSeeder extends Seeder
{
    /**
     * Run the database seeds.
     */
    public function run(): void
    {
        //
        DB::table('inspection_types')->insert([
            ['type_name' => 'Schedule 2'],
            ['type_name' => 'Schedule 3'],
        ]);
    }
}
