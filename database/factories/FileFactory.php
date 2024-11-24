<?php

namespace Database\Factories;

use Illuminate\Database\Eloquent\Factories\Factory;

/**
 * @extends \Illuminate\Database\Eloquent\Factories\Factory
 */
class FileFactory extends Factory
{
    /**
     * Define the model's default state.
     *
     * @return array<string, mixed>
     */
    public function definition(): array
    {
        return [
            'name' => $this->faker->word() . '.' . $this->faker->fileExtension(),
            'mime' => $this->faker->mimeType(),
            'url' => $this->faker->url(),
            'metadata' => [],
        ];
    }
}
