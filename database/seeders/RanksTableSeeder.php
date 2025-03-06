<?php

namespace Database\Seeders;

use Illuminate\Database\Console\Seeds\WithoutModelEvents;
use Illuminate\Database\Seeder;
use Illuminate\Support\Facades\DB;

class RanksTableSeeder extends Seeder
{
    /**
     * Run the database seeds.
     */
    public function run(): void
    {
        //
        DB::table('ranks')->insert([
            ['rank_name' => 'Sr. Demilitarization Officer (P-3)'],
            ['rank_name' => 'Senior Policy Officer (P-4)'],
            ['rank_name' => 'Inspector (D-1)'],
            ['rank_name' => 'Inspector (D-2)'],
            ['rank_name' => 'Inspector'],
            ['rank_name' => 'Inspector (P-1)'],
            ['rank_name' => 'Inspector (P-2)'],
            ['rank_name' => 'Inspector (P-3)'],
            ['rank_name' => 'Inspector (P-4)'],
            ['rank_name' => 'Inspector (P-5)'],
            ['rank_name' => 'Inspection Assistant (GS-5)'],
            ['rank_name' => 'Inspection Assistant (GS-6)'],
            ['rank_name' => 'Inspection Assistant (GS-7)'],
        ]);
    }
}
