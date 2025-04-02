<?php

namespace SebastianSulinski\Search;

use Illuminate\Support\Collection;
use SebastianSulinski\Search\Filter\Response;

interface Indexer
{
    /**
     * Initialise collections.
     */
    public function initialise(): void;

    /**
     * Index new document.
     */
    public function create(IndexableDocument $document): bool;

    /**
     * Update existing document.
     */
    public function update(IndexableDocument $document): bool;

    /**
     * Remove existing document.
     */
    public function delete(IndexableDocument $document): bool;

    /**
     * Bulk import all documents for all indexes or by index.
     */
    public function import(?string $index = null): void;

    /**
     * Bulk import all documents for all indexes or by index.
     */
    public function importChunk(?string $index, array $documents): void;

    /**
     * Export all documents for the given index.
     */
    public function export(string $index): ?array;

    /**
     * Bulk remove all documents by index.
     */
    public function purge(string $index): void;

    /**
     * Get a list of all indexes.
     */
    public function indexes(): Collection;

    /**
     * Get a list of all documents.
     */
    public function documents(string $index): Collection;

    /**
     * Search for the specific documents.
     */
    public function search(string $index, array $params): Response;
}
