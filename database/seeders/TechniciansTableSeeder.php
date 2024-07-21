<?php

namespace Database\Seeders;

use Illuminate\Database\Console\Seeds\WithoutModelEvents;
use Illuminate\Database\Seeder;
use Illuminate\Support\Facades\DB;

class TechniciansTableSeeder extends Seeder
{
    /**
     * Run the database seeds.
     */
    public function run(): void
    {
        $technician = [
            'full_name' => 'KTV Nguyễn Văn A',
            'user_id' => 2,
        ];

        DB::table('technicians')->insert($technician);
    }
}
