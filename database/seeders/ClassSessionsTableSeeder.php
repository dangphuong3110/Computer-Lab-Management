<?php

namespace Database\Seeders;

use Illuminate\Database\Console\Seeds\WithoutModelEvents;
use Illuminate\Database\Seeder;
use Illuminate\Support\Facades\DB;

class ClassSessionsTableSeeder extends Seeder
{
    /**
     * Run the database seeds.
     */
    public function run(): void
    {
        $class_sessions = [
            ['start_lesson' => '07:00:00', 'end_lesson' => '09:45:00', 'day_of_week' => 'Thứ hai', 'room_id' => 4, 'class_id' => 1],
            ['start_lesson' => '07:00:00', 'end_lesson' => '09:45:00', 'day_of_week' => 'Thứ năm', 'room_id' => 6, 'class_id' => 1],
        ];

        DB::table('class_sessions')->insert($class_sessions);

        $class_session_lessons = [
            ['session_id' => 1, 'lesson_id' => 1],
            ['session_id' => 1, 'lesson_id' => 2],
            ['session_id' => 1, 'lesson_id' => 3],
            ['session_id' => 2, 'lesson_id' => 1],
            ['session_id' => 2, 'lesson_id' => 2],
            ['session_id' => 2, 'lesson_id' => 3],
        ];

        DB::table('class_session_lesson')->insert($class_session_lessons);
    }
}
