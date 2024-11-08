<?php

namespace Workbench\Database\Factories;

use Illuminate\Database\Eloquent\Factories\Factory;
use Workbench\App\Models\Book;

/**
 * @extends \Illuminate\Database\Eloquent\Factories\Factory<\Workbench\App\Models\Book>
 */
class BookFactory extends Factory
{
    protected $model = Book::class;

    /**
     * Define the model's default state.
     *
     * @return array<string, mixed>
     */
    public function definition(): array
    {
        return [
            'name' => $this->faker->name(),
            'author' => $this->faker->name(),
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
