<?php

namespace SebastianSulinski\Search;

use Illuminate\Support\LazyCollection;

interface IndexableDocument
{
    /**
     * Name of the index / collection document belongs to.
     *
     * @return array<int, string>
     */
    public static function searchableAs(): array;

    /**
     * Get an array of all searchable documents.
     *
     * @return \Illuminate\Support\LazyCollection<array<string, mixed>>
     */
    public static function searchable(string $index): LazyCollection;

    /**
     * Get the indexable data array for the document.
     *
     * @return array<string, array<string, mixed>>
     */
    public function toSearchableArray(): array;

    /**
     * Determine if document should be searchable.
     */
    public function shouldBeSearchable(): bool;

    /**
     * Get the value used to index the document.
     */
    public function getSearchKey(): string;
}
