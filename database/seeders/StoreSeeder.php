<?php

namespace Database\Seeders;

use App\Models\Store;
use Illuminate\Database\Seeder;

class StoreSeeder extends Seeder
{
    /**
     * Run the database seeds.
     */
    public function run(): void
    {
        Store::create([
            'name' => 'Steam',
            'url' => 'https://store.steampowered.com/',
            'logo' => 'https://upload.wikimedia.org/wikipedia/commons/c/c1/Steam_Logo.png',
        ]);
    }
}
