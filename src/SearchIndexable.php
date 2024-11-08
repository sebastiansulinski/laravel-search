<?php

namespace SebastianSulinski\Search;

use Illuminate\Support\LazyCollection;

trait SearchIndexable
{
    /**
     * {@inheritDoc}
     */
    public static function searchable(string $index): LazyCollection
    {
        return static::query()
            ->orderBy('id')
            ->lazyById(
                config('search.chunk', 500),
                column: 'id'
            )->filter(
                fn (IndexableDocument $document) => $document->shouldBeSearchable()
            )->map(
                fn (IndexableDocument $document) => $document->toSearchableArray()[$index]
            );
    }

    /**
     * {@inheritDoc}
     */
    public function shouldBeSearchable(): bool
    {
        return true;
    }

    /**
     * {@inheritDoc}
     */
    public function getSearchKey(): string
    {
        return $this->getTable().'-'.$this->getKey();
    }
}
