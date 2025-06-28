<?php
namespace Database\Seeders;

use Illuminate\Database\Seeder;
use App\Models\Book;

class BookSeeder extends Seeder
{
    public function run(): void
    {
        Book::create([
            'title' => '1984',
            'author' => 'George Orwell',
            'publication_year' => 1949,
            'genre' => 'Антиутопия',
            'description' => 'Антиутопия.'
        ]);

        Book::create([
            'title' => 'Выдуманная книга',
            'author' => 'Выдуманное имя',
            'publication_year' => 1960,
            'genre' => 'Фэнтези',
            'description' => 'Выдуманное описание.'
        ]);
        // Добавить больше тестовых данных
    }
}