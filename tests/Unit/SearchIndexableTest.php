<?php

namespace SebastianSulinski\SearchTests\Unit;

use Illuminate\Database\Eloquent\Model;
use Illuminate\Foundation\Testing\RefreshDatabase;
use PHPUnit\Framework\Attributes\Test;
use SebastianSulinski\Search\Facades\Search;
use SebastianSulinski\Search\IndexableDocument;
use SebastianSulinski\Search\SearchIndexable;
use SebastianSulinski\SearchTests\BaseTest;
use Workbench\App\Models\Book;
use Workbench\App\Models\Movie;

class SearchIndexableTest extends BaseTest
{
    use RefreshDatabase;

    #[Test]
    public function returns_correctly_formatted_collection_without_hidden_records(): void
    {
        $books = Book::factory(3)->create()->sortBy('id');

        Book::factory()->hidden()->create();

        $this->assertDatabaseCount(Book::class, 4);

        $this->assertEquals([
            $books[0]->toSearchableArray()['global_search'],
            $books[1]->toSearchableArray()['global_search'],
            $books[2]->toSearchableArray()['global_search'],
        ], Book::searchable('global_search')->toArray());
    }

    #[Test]
    public function returns_correct_list_of_indexes(): void
    {
        $customClass = new class extends Model implements IndexableDocument
        {
            use SearchIndexable;

            public static function searchableAs(): array
            {
                return ['custom_search'];
            }

            public function toSearchableArray(): array
            {
                return [];
            }
        };

        config([
            'search' => [
                'default' => null,
                'models' => [
                    Book::class,
                    Movie::class,
                    $customClass::class,
                ],
            ],
        ]);

        $this->assertEquals(
            ['global_search', 'custom_search'],
            Search::availableIndexes()
        );
    }
}
