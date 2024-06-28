<?php

namespace Database\Seeders;

use Illuminate\Database\Console\Seeds\WithoutModelEvents;
use Illuminate\Database\Seeder;
use Illuminate\Support\Facades\DB;

class ComputersTableSeeder extends Seeder
{
    /**
     * Run the database seeds.
     */
    public function run(): void
    {
        for ($i = 1; $i <= 16; $i++){
            for ($j = 1; $j <= 40; $j++){
                $computer = [
                    'position' => $j,
                    'configuration' => 'Intel Core i7, 16GB RAM, 512GB SSD',
                    'purchase_date' => '2021-01-12',
                    'status' => 'available',
                    'room_id' => $i,
                ];

                DB::table('computers')->insert($computer);
            }
        }
    }
}
