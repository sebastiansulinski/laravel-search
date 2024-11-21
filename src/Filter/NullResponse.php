<?php

namespace SebastianSulinski\Search\Filter;

use Illuminate\Support\Collection;

class NullResponse extends Response
{
    /**
     * NullResponse constructor.
     */
    public function __construct(string $index, mixed $query)
    {
        parent::__construct(
            new Collection,
            new Paging(
                totalRecords: 0,
                matchingRecords: 0,
                currentPage: 1,
                perPage: 10
            ),
            new RequestParams(
                index: $index,
                query: $query
            )
        );
    }
}
