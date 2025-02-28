<?php

namespace Database\Seeders;

use App\Models\Status;
use Illuminate\Database\Seeder;

class StatusSeeder extends Seeder
{
    /**
     * Run the database seeds.
     *
     * @return void
     */
    public function run(): void
    {
        Status::create([
            'name' => 'available',
        ]);
        Status::create([
            'name' => 'unavailable',
        ]);
        Status::create([
            'name' => 'cracked',
        ]);
        Status::create([
            'name' => 'uncracked',
        ]);
        Status::create([
            'name' => 'cracked-oneday',
        ]);
    }
}
