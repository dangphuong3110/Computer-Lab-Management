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
            ['name' => '205', 'capacity' => 45, 'building_id' => 1],
            ['name' => '206', 'capacity' => 45, 'building_id' => 1],
            ['name' => '207', 'capacity' => 45, 'building_id' => 1],
            ['name' => '208', 'capacity' => 45, 'building_id' => 1],
            ['name' => '209', 'capacity' => 45, 'building_id' => 1],
            ['name' => '210', 'capacity' => 45, 'building_id' => 1],
            ['name' => '102', 'capacity' => 45, 'building_id' => 2],
            ['name' => '201', 'capacity' => 45, 'building_id' => 2],
            ['name' => '202', 'capacity' => 45, 'building_id' => 2],
            ['name' => '301', 'capacity' => 45, 'building_id' => 2],
            ['name' => '302', 'capacity' => 45, 'building_id' => 2],
            ['name' => '401', 'capacity' => 45, 'building_id' => 2],
            ['name' => '402', 'capacity' => 45, 'building_id' => 2],
            ['name' => '403', 'capacity' => 45, 'building_id' => 2],
        ];

        DB::table('rooms')->insert($rooms);
    }
}
