<?php

namespace Database\Seeders;

use Illuminate\Database\Seeder;
use App\Models\Application;

class DatabaseSeeder extends Seeder
{
    /**
     * Seed the application's database.
     *
     * @return void
     */
    public function run()
    {
        $this->call([
            ConfigurationSeeder::class,
            AdminSeeder::class,
            RolePermissionSeeder::class,
            UserSeeder::class,
        ]);
        Application::factory()->create();

    }
}
