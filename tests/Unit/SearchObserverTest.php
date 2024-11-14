<?php

namespace SebastianSulinski\SearchTests\Unit;

use Illuminate\Foundation\Testing\RefreshDatabase;
use Override;
use PHPUnit\Framework\Attributes\Test;
use SebastianSulinski\Search\Indexer;
use SebastianSulinski\SearchTests\BaseTest;
use Workbench\App\Models\Book;

class SearchObserverTest extends BaseTest
{
    use RefreshDatabase;

    private Book $book;

    #[Test]
    public function created_event_triggers_indexer_create_method(): void
    {
        $spy = $this->spy(Indexer::class);

        $book = Book::create([
            'name' => 'Test Book',
            'author' => 'Test Author',
            'description' => 'Test Description',
        ]);

        $spy->shouldHaveReceived('create', [$book]);
    }

    #[Test]
    public function updated_event_triggers_indexer_update_method(): void
    {
        $spy = $this->spy(Indexer::class);

        $this->book->update(['name' => 'My Book']);

        $spy->shouldHaveReceived('update', [$this->book]);
    }

    #[Test]
    public function deleted_event_triggers_indexer_delete_method(): void
    {
        $spy = $this->spy(Indexer::class);

        $this->book->delete();

        $spy->shouldHaveReceived('delete', [$this->book]);
    }

    /**
     * {@inheritDoc}
     */
    #[Override]
    protected function setUp(): void
    {
        parent::setUp();

        $this->book = Book::create([
            'name' => 'Test Book',
            'author' => 'Test Author',
            'description' => 'Test Description',
        ]);
    }
}
