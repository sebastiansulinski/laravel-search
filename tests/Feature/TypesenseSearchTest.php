<?php

namespace SebastianSulinski\SearchTests\Feature;

use Illuminate\Foundation\Testing\RefreshDatabase;
use PHPUnit\Framework\Attributes\Test;
use SebastianSulinski\SearchTests\BaseTest;
use Typesense\ApiCall;
use Typesense\Client;
use Typesense\Collection;
use Typesense\Collections;
use Typesense\Documents;
use Typesense\Lib\Configuration;

class TypesenseSearchTest extends BaseTest
{
    use RefreshDatabase;

    #[Test]
    public function returns_pagination_with_correct_values(): void
    {
        config([
            'search' => array_merge(
                require __DIR__.'/../../src/config/search.php',
                [
                    'default' => 'typesense',
                    'indexes' => 'global_search',
                ]),
        ]);

        $this->instance(Client::class, new class extends Client
        {
            public function __construct()
            {
                $config = require __DIR__.'/../../workbench/config/services.php';

                $this->collections = new Collections(new ApiCall(
                    new Configuration($config['typesense'])
                ));

                $this->collections['global_search'] = new class extends Collection
                {
                    public function __construct()
                    {
                        $this->documents = new class extends Documents
                        {
                            public function __construct() {}

                            public function search(array $searchParams): array
                            {
                                return [
                                    'hits' => [],
                                    'out_of' => 102000,
                                    'found' => 102000,
                                    'page' => 1,
                                    'request_params' => [
                                        'per_page' => 10,
                                        'collection_name' => 'global_search',
                                        'q' => 'keyword',
                                    ],
                                ];
                            }
                        };
                    }
                };
            }
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
}
