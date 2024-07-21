<?php

namespace Database\Seeders;

use Illuminate\Database\Console\Seeds\WithoutModelEvents;
use Illuminate\Database\Seeder;
use Illuminate\Support\Facades\DB;

class ManagersTableSeeder extends Seeder
{
    /**
     * Run the database seeds.
     */
    public function run(): void
    {
        $manager = [
            'full_name' => 'Cán bộ quản lý',
            'user_id' => 1,
        ];

        DB::table('managers')->insert($manager);
    }
}
