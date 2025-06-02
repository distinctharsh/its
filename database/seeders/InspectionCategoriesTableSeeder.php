<?php

namespace Database\Seeders;

use Illuminate\Database\Console\Seeds\WithoutModelEvents;
use Illuminate\Database\Seeder;
use Illuminate\Support\Facades\DB;

class InspectionCategoriesTableSeeder extends Seeder
{
    /**
     * Run the database seeds.
     */
    public function run(): void
    {
        //
        DB::table('inspection_categories')->insert([
            ['category_name' => 'Inspector – Routine Inspection'],
            ['category_name' => 'Headquarters Staff – Routine Inspection'],
            ['category_name' => 'Inspection Assistant – Routine Inspection'],
            ['category_name' => 'Inspector – Challenge Inspection'],
            ['category_name' => 'Headquarters Staff – Challenge Inspection'],
            ['category_name' => 'Inspection Assistant – Challenge Inspection'],
        ]);
    }
}
