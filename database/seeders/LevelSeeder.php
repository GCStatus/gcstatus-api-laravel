<?php

namespace Database\Seeders;

use App\Models\Level;
use Illuminate\Database\Seeder;

class LevelSeeder extends Seeder
{
    /**
     * Run the database seeds.
     *
     * @return void
     */
    public function run(): void
    {
        Level::create([
            'level' => 1,
            'coins' => 0,
            'experience' => 0,
        ]);
        Level::create([
            'level' => 2,
            'coins' => 10,
            'experience' => 100,
        ]);
        Level::create([
            'level' => 3,
            'coins' => 20,
            'experience' => 200,
        ]);
        Level::create([
            'level' => 4,
            'coins' => 50,
            'experience' => 500,
        ]);
        Level::create([
            'level' => 5,
            'coins' => 100,
            'experience' => 1000,
        ]);
    }
}
