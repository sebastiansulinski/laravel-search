<?php

namespace SebastianSulinski\SearchTests\Feature;

use Illuminate\Foundation\Testing\RefreshDatabase;
use Illuminate\Support\Collection;
use Mockery\MockInterface;
use PHPUnit\Framework\Attributes\Test;
use SebastianSulinski\Search\Facades\Search;
use SebastianSulinski\Search\Filter\Paging;
use SebastianSulinski\Search\Filter\RequestParams;
use SebastianSulinski\Search\Filter\Response;
use SebastianSulinski\Search\Indexer;
use SebastianSulinski\SearchTests\BaseTest;
use Workbench\App\Models\Book;
use Workbench\App\Models\Movie;

class SearchTest extends BaseTest
{
    use RefreshDatabase;

    #[Test]
    public function returns_empty_response_with_null_driver(): void
    {
        config([
            'search' => [
                'models' => [
                    Book::class,
                    Movie::class,
                ],
                'drivers' => [
                    'typesense' => [
                        'collections' => [],
                    ],
                ],
            ],
        ]);

        $this->post(route('search'), [
            'index' => 'global_search',
            'params' => ['q' => 'keyword'],
        ])
            ->assertOk()
            ->assertJson([
                'records' => [],
                'paging' => [
                    'totalRecordsForHumans' => 0,
                    'matchingRecordsForHumans' => 0,
                    'numberOfPages' => 0,
                    'numberOfPagesForHumans' => 0,
                    'show' => false,
                    'totalRecords' => 0,
                    'matchingRecords' => 0,
                    'currentPage' => 1,
                    'perPage' => 10,
                ],
                'params' => [
                    'index' => 'global_search',
                    'query' => '',
                ],
            ]);
    }

    #[Test]
    public function returns_pagination_with_correct_values(): void
    {
        config([
            'search' => [
                'models' => [
                    Book::class,
                    Movie::class,
                ],
            ],
        ]);

        $this->partialMock(Indexer::class, function (MockInterface $mock) {
            $mock->shouldReceive('search')->once()->andReturn(new Response(
                records: new Collection,
                paging: new Paging(
                    totalRecords: 102000,
                    matchingRecords: 102000,
                    currentPage: 1,
                    perPage: 10
                ),
                params: new RequestParams(
                    index: 'global_search',
                    query: 'keyword'
                )
            ));
        });

        $this->post(route('search'), [
            'index' => 'global_search',
            'params' => ['q' => 'keyword'],
        ])
            ->assertOk()
            ->assertJson([
                'records' => [],
                'paging' => [
                    'totalRecordsForHumans' => '102,000',
                    'matchingRecordsForHumans' => '102,000',
                    'numberOfPages' => 10200,
                    'numberOfPagesForHumans' => '10,200',
                    'show' => true,
                    'totalRecords' => 102000,
                    'matchingRecords' => 102000,
                    'currentPage' => 1,
                    'perPage' => 10,
                ],
                'params' => [
                    'index' => 'global_search',
                    'query' => 'keyword',
                ],
            ]);
    }

    #[Test]
    public function search_validation_fails_using_macro_defined_validation_rules(): void
    {
        Search::validation('global_search', [
            'params.q' => ['required'],
        ]);

        $this->post(route('search'), [
            'index' => 'global_search',
            'params' => ['a' => 'keyword'],
        ])
            ->assertInvalid('params.q');
    }
}
