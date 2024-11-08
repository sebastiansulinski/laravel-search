<?php

namespace SebastianSulinski\Search\Filter;

use Illuminate\Support\Collection;

class Response
{
    /**
     * Response constructor.
     */
    public function __construct(
        public Collection $records,
        public Paging $paging,
        public RequestParams $params
    ) {}
}
