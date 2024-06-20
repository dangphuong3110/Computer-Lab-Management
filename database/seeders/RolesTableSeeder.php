<?php

namespace Database\Seeders;

use Illuminate\Database\Console\Seeds\WithoutModelEvents;
use Illuminate\Database\Seeder;
use Illuminate\Support\Facades\DB;

class RolesTableSeeder extends Seeder
{
    /**
     * Run the database seeds.
     */
    public function run(): void
    {
        $roles = [
            ['role_name' => 'manager'],
            ['role_name' => 'technician'],
            ['role_name' => 'student'],
            ['role_name' => 'lecturer'],
        ];

        DB::table('roles')->insert($roles);
    }
}
