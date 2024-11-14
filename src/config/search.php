<?php

return [

    /*
    |--------------------------------------------------------------------------
    | Search Search Store
    |--------------------------------------------------------------------------
    |
    | This option controls the default search driver.
    |
    */

    'default' => env('SEARCH_DRIVER'),

    /*
    |--------------------------------------------------------------------------
    | List Of Search Drivers.
    |--------------------------------------------------------------------------
    |
    | The list of all available driver implementations.
    |
    */

    'drivers' => [

        'typesense' => [

            'collections' => [
                'global_search' => [
                    'schema' => [
                        'name' => 'global_search',
                        'fields' => [
                            [
                                'name' => 'type',
                                'type' => 'string',
                                'facet' => true,
                                'sort' => true,
                            ],
                            [
                                'name' => 'id',
                                'type' => 'string',
                                'sort' => true,
                            ],
                            [
                                'name' => 'name',
                                'type' => 'string',
                                'sort' => true,
                            ],
                            [
                                'name' => 'author',
                                'type' => 'string',
                                'sort' => true,
                            ],
                            [
                                'name' => 'description',
                                'type' => 'string',
                                'sort' => true,
                            ],
                            [
                                'name' => 'created_at',
                                'type' => 'int64',
                                'sort' => true,
                            ],
                        ],
                        'default_sorting_field' => 'created_at',
                    ],
                    'search-parameters' => [
                        'query_by' => implode(',', ['name', 'author', 'description']),
                    ],
                ],
            ],
        ],
    ],

    /*
    |--------------------------------------------------------------------------
    | List Of Searchable Models.
    |--------------------------------------------------------------------------
    |
    | The list of all searchable models.
    |
    */

    'models' => [
    ],

    /*
    |--------------------------------------------------------------------------
    | Model Fetch Chunk Size.
    |--------------------------------------------------------------------------
    |
    | The number of records import query should return when chunking results.
    |
    */

    'chunk' => 500,

    /*
    |--------------------------------------------------------------------------
    | Default Queue
    |--------------------------------------------------------------------------
    |
    | Default queue configuration parameters.
    |
    */

    'queue' => [
        'connection' => env('SEARCH_QUEUE_CONNECTION', env('QUEUE_CONNECTION', 'database')),
        'queue' => env('SEARCH_QUEUE', 'default'),
    ],
];
