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
                'email' => 'cbql1@gmail.com',
                'password' => Hash::make('123456'),
                'phone' => $faker->phoneNumber,
                'is_verified' => true,
                'role_id' => 1,
            ],
            [
                'email' => 'ktv1@gmail.com',
                'password' => Hash::make('123456'),
                'phone' => $faker->phoneNumber,
                'is_verified' => true,
                'role_id' => 2,
            ],
            [
                'email' => '2051063451@e.tlu.edu.vn',
                'password' => Hash::make('123456'),
                'phone' => $faker->phoneNumber,
                'is_verified' => true,
                'role_id' => 3,
            ],
            [
                'email' => 'gv1@tlu.edu.vn',
                'password' => Hash::make('123456'),
                'phone' => $faker->phoneNumber,
                'is_verified' => true,
                'role_id' => 4,
            ],
            [
                'email' => 'gv2@tlu.edu.vn',
                'password' => Hash::make('123456'),
                'phone' => $faker->phoneNumber,
                'is_verified' => true,
                'role_id' => 4,
            ],
            [
                'email' => 'gv3@tlu.edu.vn',
                'password' => Hash::make('123456'),
                'phone' => $faker->phoneNumber,
                'is_verified' => true,
                'role_id' => 4,
            ],
            [
                'email' => 'gv4@tlu.edu.vn',
                'password' => Hash::make('123456'),
                'phone' => $faker->phoneNumber,
                'is_verified' => true,
                'role_id' => 4,
            ],
            [
                'email' => 'gv5@tlu.edu.vn',
                'password' => Hash::make('123456'),
                'phone' => $faker->phoneNumber,
                'is_verified' => true,
                'role_id' => 4,
            ],
            [
                'email' => 'gv6@tlu.edu.vn',
                'password' => Hash::make('123456'),
                'phone' => $faker->phoneNumber,
                'is_verified' => true,
                'role_id' => 4,
            ],
            [
                'email' => 'gv7@tlu.edu.vn',
                'password' => Hash::make('123456'),
                'phone' => $faker->phoneNumber,
                'is_verified' => true,
                'role_id' => 4,
            ],
            [
                'email' => 'gv8@tlu.edu.vn',
                'password' => Hash::make('123456'),
                'phone' => $faker->phoneNumber,
                'is_verified' => true,
                'role_id' => 4,
            ],
            [
                'email' => 'gv9@tlu.edu.vn',
                'password' => Hash::make('123456'),
                'phone' => $faker->phoneNumber,
                'is_verified' => true,
                'role_id' => 4,
            ],
            [
                'email' => 'gv10@tlu.edu.vn',
                'password' => Hash::make('123456'),
                'phone' => $faker->phoneNumber,
                'is_verified' => true,
                'role_id' => 4,
            ],
        ];

        DB::table('users')->insert($users);
    }
}
