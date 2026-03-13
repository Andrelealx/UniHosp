<?php

namespace Database\Seeders;

use Illuminate\Database\Seeder;

class DatabaseSeeder extends Seeder
{
    /**
     * Seed the application's database.
     */
    public function run(): void
    {
        $this->call([
            RolesAndPermissionsSeeder::class,
            ConveniosSeeder::class,
            SetoresSeeder::class,
            SalasSeeder::class,
            FilasSeeder::class,
            PaineisSeeder::class,
            InitialUsersSeeder::class,
            StatusSeeder::class,
        ]);
    }
}
