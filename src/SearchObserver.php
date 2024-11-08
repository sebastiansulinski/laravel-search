<?php

namespace SebastianSulinski\Search;

use Illuminate\Contracts\Events\ShouldHandleEventsAfterCommit;

readonly class SearchObserver implements ShouldHandleEventsAfterCommit
{
    /**
     * SearchObserver constructor.
     */
    public function __construct(private Indexer $indexer) {}

    /**
     * Action to perform when new record is created.
     */
    public function created(IndexableDocument $model): void
    {
        $this->indexer->create($model);
    }

    /**
     * Action to perform when record has been updated.
     */
    public function updated(IndexableDocument $model): void
    {
        $this->indexer->update($model);
    }

    /**
     * Action to perform when record has been removed.
     */
    public function deleted(IndexableDocument $model): void
    {
        $this->indexer->delete($model);
    }
}
