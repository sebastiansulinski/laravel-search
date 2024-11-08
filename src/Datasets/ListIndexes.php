<?php

namespace SebastianSulinski\Search\Datasets;

use SebastianSulinski\Search\Indexer;

readonly class ListIndexes
{
    /**
     * ListCollections controller.
     */
    public function __construct(private Indexer $indexer) {}

    /**
     * Get dataset.
     */
    public function get(): array
    {
        return [
            'indexes' => $this->indexer->indexes()->map(
                fn (array $index) => array_merge($index, [
                    'documents' => $this->indexer->documents($index['name'])['hits'],
                ])
            ),
        ];
    }
}
