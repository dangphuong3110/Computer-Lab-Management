<?php

namespace Database\Seeders;

use Illuminate\Database\Console\Seeds\WithoutModelEvents;
use Illuminate\Database\Seeder;
use Illuminate\Support\Facades\DB;

class LessonsTableSeeder extends Seeder
{
    /**
     * Run the database seeds.
     */
    public function run(): void
    {
        $lessons = [
            ['start_time' => '07:00:00', 'end_time' => '07:50:00'],
            ['start_time' => '07:55:00', 'end_time' => '08:45:00'],
            ['start_time' => '08:50:00', 'end_time' => '09:40:00'],
            ['start_time' => '09:45:00', 'end_time' => '10:35:00'],
            ['start_time' => '10:40:00', 'end_time' => '11:30:00'],
            ['start_time' => '11:35:00', 'end_time' => '12:25:00'],
            ['start_time' => '12:55:00', 'end_time' => '13:45:00'],
            ['start_time' => '13:50:00', 'end_time' => '14:40:00'],
            ['start_time' => '14:45:00', 'end_time' => '15:35:00'],
            ['start_time' => '15:40:00', 'end_time' => '16:30:00'],
            ['start_time' => '16:35:00', 'end_time' => '17:25:00'],
            ['start_time' => '17:30:00', 'end_time' => '18:20:00'],
            ['start_time' => '18:50:00', 'end_time' => '19:40:00'],
            ['start_time' => '19:45:00', 'end_time' => '20:35:00'],
            ['start_time' => '20:40:00', 'end_time' => '21:30:00'],
        ];

        DB::table('lessons')->insert($lessons);
    }
}
