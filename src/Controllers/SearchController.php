<?php

namespace SebastianSulinski\Search\Controllers;

use Illuminate\Routing\Controller;
use SebastianSulinski\Search\Actions\Search;
use SebastianSulinski\Search\Requests\SearchRequest;

class SearchController extends Controller
{
    /**
     * {@inheritDoc}
     */
    public function __invoke(SearchRequest $request, Search $action): array
    {
        return (array) $action->handle(
            $request->index, $request->payload()
        );
    }
}
