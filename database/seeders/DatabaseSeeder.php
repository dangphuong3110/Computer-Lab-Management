<?php

namespace Database\Seeders;

// use Illuminate\Database\Console\Seeds\WithoutModelEvents;
use App\Models\CreditClass;
use Illuminate\Database\Seeder;

class DatabaseSeeder extends Seeder
{
    /**
     * Seed the application's database.
     */
    public function run(): void
    {
        $this->call([
            RolesTableSeeder::class,
            UsersTableSeeder::class,
            StudentsTableSeeder::class,
            LecturersTableSeeder::class,
            TechniciansTableSeeder::class,
            ManagersTableSeeder::class,
            BuildingsTableSeeder::class,
            RoomsTableSeeder::class,
            CreditClassesTableSeeder::class,
            LessonsTableSeeder::class,
            ClassSessionsTableSeeder::class,
        ]);

        // \App\Models\User::factory(10)->create();

        // \App\Models\User::factory()->create([
        //     'name' => 'Test User',
        //     'email' => 'test@example.com',
        // ]);
    }
}
