<?php

namespace Database\Seeders;

use App\Models\Community;
// use Illuminate\Database\Console\Seeds\WithoutModelEvents;
use Illuminate\Database\Seeder;

class DatabaseSeeder extends Seeder
{
    /**
     * Seed the application's database.
     */
    public function run(): void
    {
        try {
            Community::factory()->create([
                'name' => 'Switch 345',
                'slug' => 'switch-345',
            ]);

            Community::factory()->create([
                'name' => 'Fintual Palace',
                'slug' => 'fintual-palace',
            ]);
        } catch (\Exception $e) {
            dump('Error creating community:', $e->getMessage());
        }
    }
}
