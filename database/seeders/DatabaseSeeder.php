<?php

namespace Database\Seeders;

use Illuminate\Database\Seeder;

class DatabaseSeeder extends Seeder
{
    /**
     * Seed the application's database.
     *
     * @return void
     */
    public function run(): void
    {
        $this->call([
            LevelSeeder::class,
            UserSeeder::class,
            TransactionTypeSeeder::class,
            StatusSeeder::class,
            MediaTypeSeeder::class,
            StoreSeeder::class,
            RequirementTypeSeeder::class,
            PlatformSeeder::class,
        ]);
    }
}
