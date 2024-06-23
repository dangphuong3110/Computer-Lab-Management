<?php

namespace Database\Seeders;

use App\Models\CreditClass;
use Illuminate\Database\Console\Seeds\WithoutModelEvents;
use Illuminate\Database\Seeder;
use Illuminate\Support\Facades\DB;

class CreditClassesTableSeeder extends Seeder
{
    /**
     * Run the database seeds.
     */
    public function run(): void
    {
        $class = [
            'name' => 'Lập trình nâng cao (64TH.NB-1)',
            'start_date' => '2024-01-22',
            'end_date' => '2024-03-31',
            'class_code' => '123123',
            'lecturer_id' => 1,
        ];

        DB::table('classes')->insert($class);
    }
}
