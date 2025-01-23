<?php

namespace Database\Seeders;

use App\Models\Platform;
use Illuminate\Support\Str;
use Illuminate\Database\Seeder;

class PlatformSeeder extends Seeder
{
    /**
     * Run the database seeds.
     */
    public function run(): void
    {
        Platform::create([
            'name' => $name = 'Windows',
            'slug' => Str::slug($name),
        ]);
        Platform::create([
            'name' => $name = 'MacOS',
            'slug' => Str::slug($name),
        ]);
        Platform::create([
            'name' => $name = 'Linux',
            'slug' => Str::slug($name),
        ]);
        Platform::create([
            'name' => $name = 'PlayStation 1',
            'slug' => Str::slug($name),
        ]);
        Platform::create([
            'name' => $name = 'PlayStation 2',
            'slug' => Str::slug($name),
        ]);
        Platform::create([
            'name' => $name = 'PlayStation 3',
            'slug' => Str::slug($name),
        ]);
        Platform::create([
            'name' => $name = 'PlayStation 4',
            'slug' => Str::slug($name),
        ]);
        Platform::create([
            'name' => $name = 'PlayStation 5',
            'slug' => Str::slug($name),
        ]);
        Platform::create([
            'name' => $name = 'Xbox',
            'slug' => Str::slug($name),
        ]);
        Platform::create([
            'name' => $name = 'Xbox 360',
            'slug' => Str::slug($name),
        ]);
        Platform::create([
            'name' => $name = 'Xbox One',
            'slug' => Str::slug($name),
        ]);
        Platform::create([
            'name' => $name = 'Xbox One S',
            'slug' => Str::slug($name),
        ]);
        Platform::create([
            'name' => $name = 'Xbox One X',
            'slug' => Str::slug($name),
        ]);
        Platform::create([
            'name' => $name = 'Xbox Series S',
            'slug' => Str::slug($name),
        ]);
        Platform::create([
            'name' => $name = 'Xbox Series X',
            'slug' => Str::slug($name),
        ]);
        Platform::create([
            'name' => $name = 'Xbox Game Cloud',
            'slug' => Str::slug($name),
        ]);
        Platform::create([
            'name' => $name = 'Nintendo Switch',
            'slug' => Str::slug($name),
        ]);
        Platform::create([
            'name' => $name = 'Google Stadia',
            'slug' => Str::slug($name),
        ]);
        Platform::create([
            'name' => $name = 'Amazon Luna',
            'slug' => Str::slug($name),
        ]);
        Platform::create([
            'name' => $name = 'GeForce Now',
            'slug' => Str::slug($name),
        ]);
    }
}
