<?php

namespace Database\Seeders;

use Illuminate\Database\Console\Seeds\WithoutModelEvents;
use Illuminate\Database\Seeder;
use Illuminate\Support\Facades\DB;
use Faker\Factory as Faker;

class StudentsTableSeeder extends Seeder
{
    /**
     * Run the database seeds.
     */
    public function run(): void
    {
        $faker = Faker::create();

        $student = [
            'full_name' => $faker->name,
            'student_code' => '2051063453',
            'class' => '62TH5',
            'gender' => 'Nam',
            'date_of_birth' => '2002-10-31',
            'user_id' => 3,
        ];

        DB::table('students')->insert($student);
    }
}
