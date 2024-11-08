<?php

namespace SebastianSulinski\Search\Drivers;

use Illuminate\Support\Collection;
use SebastianSulinski\Search\Filter\Paging;
use SebastianSulinski\Search\Filter\RequestParams;
use SebastianSulinski\Search\Filter\Response;
use SebastianSulinski\Search\IndexableDocument;
use SebastianSulinski\Search\Indexer;

class NullDriver implements Indexer
{
    /**
     * {@inheritDoc}
     */
    public function create(IndexableDocument $document): bool
    {
        return true;
    }

    /**
     * {@inheritDoc}
     */
    public function update(IndexableDocument $document): bool
    {
        return true;
    }

    /**
     * {@inheritDoc}
     */
    public function delete(IndexableDocument $document): bool
    {
        return true;
    }

    /**
     * {@inheritDoc}
     */
    public function import(?string $index = null): void {}

    /**
     * {@inheritDoc}
     */
    public function importChunk(?string $index, array $documents): void {}

    /**
     * {@inheritDoc}
     */
    public function purge(string $index): void {}

    /**
     * {@inheritDoc}
     */
    public function indexes(): Collection
    {
        return new Collection;
    }

    /**
     * {@inheritDoc}
     */
    public function documents(string $index): Collection
    {
        return new Collection;
    }

    /**
     * {@inheritDoc}
     */
    public function search(string $index, array $params): Response
    {
        return new Response(
            records: new Collection,
            paging: new Paging(
                totalRecords: 0,
                matchingRecords: 0,
                currentPage: 1,
                perPage: 10
            ),
            params: new RequestParams(
                index: $index,
                query: ''
            )
        );
    }
}
