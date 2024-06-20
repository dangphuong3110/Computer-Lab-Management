<?php

namespace Database\Seeders;

use Illuminate\Database\Console\Seeds\WithoutModelEvents;
use Illuminate\Database\Seeder;
use Illuminate\Support\Facades\DB;

class LecturersTableSeeder extends Seeder
{
    /**
     * Run the database seeds.
     */
    public function run(): void
    {
        $lecturer = [
            'full_name' => 'Nguyễn Văn A',
            'academic_rank' => 'Tiến sĩ',
            'faculty' => 'Công nghệ thông tin',
            'position' => 'Phó trưởng khoa',
            'department' => 'Hệ thống thông tin',
            'user_id' => 4,
        ];

        DB::table('lecturers')->insert($lecturer);
    }
}
