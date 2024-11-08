<?php

namespace SebastianSulinski\SearchTests\Unit;

use Illuminate\Foundation\Testing\RefreshDatabase;
use PHPUnit\Framework\Attributes\Test;
use SebastianSulinski\SearchTests\BaseTest;
use Workbench\App\Models\Book;

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
}
