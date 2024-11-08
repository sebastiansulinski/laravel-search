<?php

namespace SebastianSulinski\Search\Actions;

use SebastianSulinski\Search\Filter\Response;
use SebastianSulinski\Search\Indexer;

readonly class Search
{
    /**
     * Search constructor.
     */
    public function __construct(private Indexer $indexer) {}

    /**
     * Handle request.
     */
    public function handle(string $index, array $payload): Response
    {
        return $this->indexer->search($index, $payload);
    }
}
