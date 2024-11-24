<?php

namespace Database\Seeders;

use App\Models\Community;
use App\Models\File;
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
            File::factory()->create([
                'id' => 1,
                'url' => 'https://opencomunidad.d1a953afcfb9fdd29a465464d677e606.r2.cloudflarestorage.com/community-logos/1732422574_image.png',
            ]);
            File::factory()->create([
                'id' => 2,
                'url' => 'https://opencomunidad.d1a953afcfb9fdd29a465464d677e606.r2.cloudflarestorage.com/community-banners/1732415516_switch-banner.png',
            ]);

            Community::factory()->create([
                'name' => 'Switch 345',
                'slug' => 'switch-345',
                'logo_id' => 1,
                'banner_id' => 2,
            ]);

            File::factory()->create([
                'id' => 3,
                'url' => 'https://opencomunidad.d1a953afcfb9fdd29a465464d677e606.r2.cloudflarestorage.com/community-logos/1732415686_fintual.png',
            ]);

            File::factory()->create([
                'id' => 4,
                'url' => 'https://opencomunidad.d1a953afcfb9fdd29a465464d677e606.r2.cloudflarestorage.com/community-banners/1732415495_oficina-fintual-resize.webp',
            ]);

            Community::factory()->create([
                'name' => 'Fintual Palace',
                'slug' => 'fintual-palace',
                'logo_id' => 3,
                'banner_id' => 4,
            ]);
        } catch (\Exception $e) {
            dump('Error creating community:', $e->getMessage());
        }
    }
}
