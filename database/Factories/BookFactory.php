<?php
namespace Database\Factories;

use App\Models\Book;
use Illuminate\Database\Eloquent\Factories\Factory;

class BookFactory extends Factory
{
    protected $model = Book::class;

    public function definition()
    {
        return [
            'title' => $this->faker->sentence(3),
            'author' => $this->faker->name,
            'publication_year' => $this->faker->year,
            'genre' => $this->faker->randomElement(['Фэнтези', 'Антиутопия', 'Научная фантастика']),
            'description' => $this->faker->paragraph,
        ];
    }
}