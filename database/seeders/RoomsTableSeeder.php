<?php

namespace Database\Seeders;

use Illuminate\Database\Console\Seeds\WithoutModelEvents;
use Illuminate\Database\Seeder;
use Illuminate\Support\Facades\DB;

class RoomsTableSeeder extends Seeder
{
    /**
     * Run the database seeds.
     */
    public function run(): void
    {
        $rooms = [
            ['name' => '205', 'number_of_computer_rows' => 4, 'max_computers_per_row' => 10, 'building_id' => 1],
            ['name' => '206', 'number_of_computer_rows' => 4, 'max_computers_per_row' => 10, 'building_id' => 1],
            ['name' => '207', 'number_of_computer_rows' => 4, 'max_computers_per_row' => 10, 'building_id' => 1],
            ['name' => '208', 'number_of_computer_rows' => 4, 'max_computers_per_row' => 10, 'building_id' => 1],
            ['name' => '209', 'number_of_computer_rows' => 4, 'max_computers_per_row' => 10, 'building_id' => 1],
            ['name' => '210', 'number_of_computer_rows' => 4, 'max_computers_per_row' => 10, 'building_id' => 1],
            ['name' => '102', 'number_of_computer_rows' => 4, 'max_computers_per_row' => 15, 'building_id' => 2],
            ['name' => '201', 'number_of_computer_rows' => 4, 'max_computers_per_row' => 15, 'building_id' => 2],
            ['name' => '202', 'number_of_computer_rows' => 4, 'max_computers_per_row' => 15, 'building_id' => 2],
            ['name' => '301', 'number_of_computer_rows' => 4, 'max_computers_per_row' => 15, 'building_id' => 2],
            ['name' => '302', 'number_of_computer_rows' => 4, 'max_computers_per_row' => 15, 'building_id' => 2],
            ['name' => '401', 'number_of_computer_rows' => 4, 'max_computers_per_row' => 15, 'building_id' => 2],
            ['name' => '402', 'number_of_computer_rows' => 4, 'max_computers_per_row' => 15, 'building_id' => 2],
            ['name' => '403', 'number_of_computer_rows' => 4, 'max_computers_per_row' => 15, 'building_id' => 2],
        ];

        DB::table('rooms')->insert($rooms);
    }
}
