<?php

namespace Database\Seeders;

use Illuminate\Database\Console\Seeds\WithoutModelEvents;
use Illuminate\Database\Seeder;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Hash;
use Faker\Factory as Faker;

class UsersTableSeeder extends Seeder
{
    /**
     * Run the database seeds.
     */
    public function run(): void
    {
        $faker = Faker::create();

        $users = [
            [
                'username' => 'cbql1',
                'email' => 'cbql1@gmail.com',
                'password' => Hash::make('123456'),
                'phone' => $faker->phoneNumber,
                'role_id' => 1,
            ],
            [
                'username' => 'ktv1',
                'email' => 'ktv1@gmail.com',
                'password' => Hash::make('123456'),
                'phone' => $faker->phoneNumber,
                'role_id' => 2,
            ],
            [
                'username' => 'sv1',
                'email' => 'sv1@gmail.com',
                'password' => Hash::make('123456'),
                'phone' => $faker->phoneNumber,
                'role_id' => 3,
            ],
            [
                'username' => 'gv1',
                'email' => 'gv1@gmail.com',
                'password' => Hash::make('123456'),
                'phone' => $faker->phoneNumber,
                'role_id' => 4,
            ]
        ];

        DB::table('users')->insert($users);
    }
}
