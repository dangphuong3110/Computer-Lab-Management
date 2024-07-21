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
        $lecturers = [
            [
                'full_name' => 'GV Trần Văn A',
                'academic_rank' => 'Tiến sĩ',
                'faculty' => 'Công nghệ thông tin',
                'position' => '',
                'department' => 'Hệ thống thông tin',
                'user_id' => 4,
            ],
            [
                'full_name' => 'GV Nguyễn Văn B',
                'academic_rank' => 'Thạc sĩ',
                'faculty' => 'Công nghệ thông tin',
                'position' => '',
                'department' => '',
                'user_id' => 5,
            ],
            [
                'full_name' => 'GV Nguyễn Văn C',
                'academic_rank' => 'Thạc sĩ',
                'faculty' => 'Công nghệ thông tin',
                'position' => '',
                'department' => 'Trí tuệ nhân tạo',
                'user_id' => 6,
            ],
            [
                'full_name' => 'GV Nguyễn Văn D',
                'academic_rank' => 'Tiến sĩ',
                'faculty' => 'Công nghệ thông tin',
                'position' => 'Trưởng khoa',
                'department' => 'Kỹ thuật phần mềm',
                'user_id' => 7,
            ],
            [
                'full_name' => 'GV Trần Văn E',
                'academic_rank' => 'Thạc sĩ',
                'faculty' => 'Công nghệ thông tin',
                'position' => '',
                'department' => 'Kỹ thuật phần mềm',
                'user_id' => 8,
            ],
            [
                'full_name' => 'GV Nguyễn Thị G',
                'academic_rank' => 'Thạc sĩ',
                'faculty' => 'Công nghệ thông tin',
                'position' => '',
                'department' => 'Kỹ thuật phần mềm',
                'user_id' => 9,
            ],
            [
                'full_name' => 'GV Nguyễn Thị H',
                'academic_rank' => 'Thạc sĩ',
                'faculty' => 'Công nghệ thông tin',
                'position' => '',
                'department' => 'Kỹ thuật phần mềm',
                'user_id' => 10,
            ],
            [
                'full_name' => 'GV Nguyễn Thị I',
                'academic_rank' => 'Thạc sĩ',
                'faculty' => 'Công nghệ thông tin',
                'position' => '',
                'department' => 'Kỹ thuật phần mềm',
                'user_id' => 11,
            ],
            [
                'full_name' => 'GV Nguyễn Thị J',
                'academic_rank' => 'Thạc sĩ',
                'faculty' => 'Công nghệ thông tin',
                'position' => '',
                'department' => 'Kỹ thuật phần mềm',
                'user_id' => 12,
            ],
            [
                'full_name' => 'GV Nguyễn Thị K',
                'academic_rank' => 'Thạc sĩ',
                'faculty' => 'Công nghệ thông tin',
                'position' => '',
                'department' => '',
                'user_id' => 13,
            ],
        ];

        DB::table('lecturers')->insert($lecturers);
    }
}
