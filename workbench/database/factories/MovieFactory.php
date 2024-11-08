<?php

namespace Workbench\Database\Factories;

use Illuminate\Database\Eloquent\Factories\Factory;
use Workbench\App\Models\Movie;

/**
 * @extends \Illuminate\Database\Eloquent\Factories\Factory<\Workbench\App\Models\Movie>
 */
class MovieFactory extends Factory
{
    protected $model = Movie::class;

    /**
     * Define the model's default state.
     *
     * @return array<string, mixed>
     */
    public function definition(): array
    {
        return [
            'title' => $this->faker->sentence,
            'director' => $this->faker->name(),
            'description' => $this->faker->text(),
        ];
    }

    /**
     * Hide record.
     */
    public function hidden(): Factory
    {
        return $this->state(fn () => [
            'searchable' => false,
        ]);
    }
}
