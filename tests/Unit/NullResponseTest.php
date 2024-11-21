<?php

namespace SebastianSulinski\SearchTests\Unit;

use Illuminate\Support\Collection;
use PHPUnit\Framework\Attributes\Test;
use SebastianSulinski\Search\Filter\NullResponse;
use SebastianSulinski\Search\Filter\Paging;
use SebastianSulinski\Search\Filter\RequestParams;
use SebastianSulinski\SearchTests\BaseTest;

class NullResponseTest extends BaseTest
{
    #[Test]
    public function returns_correct_response(): void
    {
        $this->assertEquals([
            'records' => new Collection,
            'paging' => new Paging(
                totalRecords: 0,
                matchingRecords: 0,
                currentPage: 1,
                perPage: 10
            ),
            'params' => new RequestParams(
                'my_search_index',
                'forest',
            ),
        ], (array) new NullResponse('my_search_index', 'forest'));
    }
}
