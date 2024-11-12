<?php

namespace Database\Seeders;

use App\Models\User;
use Illuminate\Database\Seeder;

class UserSeeder extends Seeder
{
    /**
     * Run the database seeds.
     *
     * @return void
     */
    public function run(): void
    {
        User::factory()->withLevel()->create([
            'nickname' => 'admin',
            'email' => 'admin@gmail.com',
        ]);

        User::factory(10)->withLevel()->create();
    }
}
