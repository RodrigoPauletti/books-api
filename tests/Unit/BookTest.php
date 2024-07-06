<?php

namespace Tests\Unit;

use Tests\TestCase;
use PHPUnit\Framework\Attributes\Test;

use App\Models\Book;
use App\Models\User;

class BookTest extends TestCase
{

    private string $booksEndpoint = '/api/v1/books';
    private string $importBooksIndicesEndpoint = '/api/v1/books/{bookId}/import-indices-xml';

    #[Test]
    public function guest_user_cant_list_books() {
        // Act
        $response = $this->getJson($this->booksEndpoint);

        // Assert
        $response->assertStatus(401);
    }

    #[Test]
    public function auth_user_can_list_books() {
        // Arrange
        $user = User::factory()->create();

        // Act
        $response = $this->actingAs($user)->getJson($this->booksEndpoint);

        // Assert
        $response->assertStatus(200);
    }

    #[Test]
    public function guest_user_cant_create_book() {
        // Act
        $response = $this
            ->postJson($this->booksEndpoint, [
                'title' => 'book title',
                'indices' => [
                    [
                        'title' => 'indice 1',
                        'page' => 2,
                        'subindices' => [
                            [
                                'title' => 'indice 1.1',
                                'page' => 3,
                                'subindices' => []
                            ]
                        ]
                    ], [
                        'title' => 'indice 2',
                        'page' => 4,
                        'subindices' => []
                    ]
                ],
            ]);

        // Assert
        $response->assertStatus(401);
    }

    #[Test]
    public function auth_user_cant_create_invalid_book() {
        // Arrange
        $user = User::factory()->create();

        // Act
        $response = $this
            ->actingAs($user)
            ->postJson($this->booksEndpoint, [
                'title' => 'book title',
                'indices' => [
                    [
                        'title' => 'indice 1',
                        'page' => 2,
                        'subindices' => [
                            // the error of the data is that this item needs to be inside an array
                            'title' => 'indice 1.1',
                            'page' => 3,
                            'subindices' => []
                        ]
                    ], [
                        'title' => 'indice 2',
                        'page' => 4,
                        'subindices' => []
                    ]
                ],
            ]);

        // Assert
        $response->assertStatus(422);
    }

    #[Test]
    public function auth_user_can_create_valid_book() {
        // Arrange
        $user = User::factory()->create();

        // Act
        $response = $this
            ->actingAs($user)
            ->postJson($this->booksEndpoint, [
                'title' => 'book title',
                'indices' => [
                    [
                        'title' => 'indice 1',
                        'page' => 2,
                        'subindices' => [
                            [
                                'title' => 'indice 1.1',
                                'page' => 3,
                                'subindices' => []
                            ]
                        ]
                    ], [
                        'title' => 'indice 2',
                        'page' => 4,
                        'subindices' => []
                    ]
                ],
            ]);

        // Assert
        $response->assertStatus(201);
    }

    #[Test]
    public function guest_user_cant_import_book() {
        // Arrange
        $this->importBooksIndicesEndpoint = preg_replace('/\{bookId\}/', '1', $this->importBooksIndicesEndpoint);

        // Act
        $response = $this->postJson($this->importBooksIndicesEndpoint);

        // Assert
        $response->assertStatus(401);
    }

    #[Test]
    public function auth_user_cant_import_invalid_book() {
        // Arrange
        $user = User::factory()->create();
        $book = Book::factory()->create();

        $this->importBooksIndicesEndpoint = preg_replace('/\{bookId\}/', $book->id, $this->importBooksIndicesEndpoint);

        $xmlData = '<indice>
            <item page="1" title="Seção 1">
                <item page="1" title="Seção 1.1">
                    <item page="1" title="Seção 1.1.1">
                        <item page="1" title="Seção 1.1.1.1">
                            <item page="1" title="Seção 1.1.1.1.1"/>
                        </item>
                    </item>
                    <item page="1" title="Seção 1.1.2">
                </item>
                <item page="2" title="Seção 1.2"/>
            </item>
            <item page="2" title="Seção 2"/>
            <item page="3" title="Seção 3"/>
        </indice>';

        // The line `<item page="1" title="Seção 1.1.2">` is incorrect (needs to end the tag with `/>`)

        // Act
        $response = $this
            ->actingAs($user)
            ->call('POST', $this->importBooksIndicesEndpoint, [], [], [], [], $xmlData);

        // Assert
        $response->assertStatus(500);
    }

    #[Test]
    public function auth_user_can_import_valid_book() {
        // Arrange
        $user = User::factory()->create();
        $book = Book::factory()->create();

        $this->importBooksIndicesEndpoint = preg_replace('/\{bookId\}/', $book->id, $this->importBooksIndicesEndpoint);

        $xmlData = '<indice>
            <item page="1" title="Seção 1">
                <item page="1" title="Seção 1.1">
                    <item page="1" title="Seção 1.1.1">
                        <item page="1" title="Seção 1.1.1.1">
                            <item page="1" title="Seção 1.1.1.1.1"/>
                        </item>
                    </item>
                    <item page="1" title="Seção 1.1.2"/>
                </item>
                <item page="2" title="Seção 1.2"/>
            </item>
            <item page="2" title="Seção 2"/>
            <item page="3" title="Seção 3"/>
        </indice>';

        // Act
        $response = $this
            ->actingAs($user)
            ->call('POST', $this->importBooksIndicesEndpoint, [], [], [], [], $xmlData);

        // Assert
        $response->assertStatus(200);
    }

}
