<?php

namespace SebastianSulinski\Search\Filter;

class RequestParams
{
    /**
     * Params constructor.
     */
    public function __construct(public string $index, public ?string $query = null) {}
}
