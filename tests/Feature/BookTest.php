<?php

namespace Tests\Feature;

use Illuminate\Foundation\Testing\RefreshDatabase;
use Tests\TestCase;
use App\Models\Book;

class BookTest extends TestCase
{
    use RefreshDatabase;

    public function test_can_list_books()
    {
        Book::factory()->count(3)->create();

        $response = $this->getJson('/api/books');

        $response->assertStatus(200)
                 ->assertJsonStructure([
                     'books' => [
                         '*' => [
                             'id', 'title', 'author', 'publication_year', 'genre', 'description'
                         ]
                     ],
                     'total'
                 ])
                 ->assertJsonCount(3, 'books');
    }

    public function test_can_create_book()
    {
        $data = [
            'title' => 'Test Book',
            'author' => 'Test Author',
            'publication_year' => 2020,
            'genre' => 'Фикшн',
            'description' => 'A test book description'
        ];

        $response = $this->postJson('/api/books', $data);

        $response->assertStatus(201)
                 ->assertJson($data);

        $this->assertDatabaseHas('books', $data);
    }

    public function test_can_show_book()
    {
        $book = Book::factory()->create();

        $response = $this->getJson("/api/books/{$book->id}");

        $response->assertStatus(200)
                 ->assertJson([
                     'id' => $book->id,
                     'title' => $book->title,
                     'author' => $book->author,
                     'publication_year' => $book->publication_year,
                     'genre' => $book->genre,
                     'description' => $book->description
                 ]);
    }

    public function test_can_update_book()
    {
        $book = Book::factory()->create();

        $data = [
            'title' => 'Updated Book',
            'author' => 'Updated Author',
            'publication_year' => 2021,
            'genre' => 'Нон-фикшн',
            'description' => 'Updated description'
        ];

        $response = $this->putJson("/api/books/{$book->id}", $data);

        $response->assertStatus(200)
                 ->assertJson($data);

        $this->assertDatabaseHas('books', $data);
    }

    public function test_can_delete_book()
    {
        $book = Book::factory()->create();

        $response = $this->deleteJson("/api/books/{$book->id}");

        $response->assertStatus(204);

        $this->assertDatabaseMissing('books', ['id' => $book->id]);
    }

    public function test_validation_fails_on_create_book()
    {
        $response = $this->postJson('/api/books', [
            'title' => '',
            'author' => '',
            'genre' => ''
        ]);

        $response->assertStatus(422)
                 ->assertJsonValidationErrors(['title', 'author', 'genre']);
    }
}
