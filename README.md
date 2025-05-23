# Lightweight search for Laravel 11 +

This package provides a simple, lightweight search component for Laravel 11+.

In contrast to the well known [Laravel Scout](https://laravel.com/docs/11.x/scout), it allows models to be associated
with more than one index with separate payload for each, which was the much needed feature for my current project.

Currently only `typesense` driver is available, but if you feel like contributing a different implementation feel free
to submit a PR.

## Installation

```bash
composer require sebastiansulinski/laravel-search
```

## Configuration

Start by publishing vendor configuration file `search.php`

```bash
php artisan vendor:publish --provider="SebastianSulinski\Search\SearchServiceProvider"
```

Within the configuration file update parameters for your selected driver and add all models to the `models` array:

```php
'models' => [
    App\Models\Book::class,
    App\Models\Movie::class,
]
```

You can also update the default queue the import job will run on using `SEARCH_QUEUE_CONNECTION` environment variable -
otherwise the default queue will be used.

## Typesense configuration

Add the following to your `services.php` config file and update accordingly:

```php
'typesense' => [
    'api_key' => env('TYPESENSE_API_KEY', 'xyz'),
    'nodes' => [
        [
            'host' => env('TYPESENSE_HOST', 'localhost'),
            'port' => env('TYPESENSE_PORT', '8108'),
            'path' => env('TYPESENSE_PATH', ''),
            'protocol' => env('TYPESENSE_PROTOCOL', 'http'),
        ],
    ],
    'nearest_node' => [
        'host' => env('TYPESENSE_HOST', 'localhost'),
        'port' => env('TYPESENSE_PORT', '8108'),
        'path' => env('TYPESENSE_PATH', ''),
        'protocol' => env('TYPESENSE_PROTOCOL', 'http'),
    ],
    'connection_timeout_seconds' => env('TYPESENSE_CONNECTION_TIMEOUT_SECONDS', 2),
    'healthcheck_interval_seconds' => env('TYPESENSE_HEALTHCHECK_INTERVAL_SECONDS', 30),
    'num_retries' => env('TYPESENSE_NUM_RETRIES', 3),
    'retry_interval_seconds' => env('TYPESENSE_RETRY_INTERVAL_SECONDS', 1),
],
```

## Initialisation

To create collections ready for indexing, run the follow command from within the terminal:

```shell
php artisan app:initialise-search
```

You can set `SEARCH_REMOVE_UNDEFINED_COLLECTIONS` variable to `true` if you'd like to remove any previously created
collections that are no longer present in the `search.php` config file while executing the above command.

## Models

Each of the models defined within the configuration file under `models` array has to implement `IndexableDocument` and
use `SearchIndexable` trait.

```php
use Illuminate\Database\Eloquent\Model;
use SebastianSulinski\Search\IndexableDocument;
use SebastianSulinski\Search\SearchIndexable;

class Book extends Model implements IndexableDocument
{
    // ...
    use SearchIndexable;
```

You will also need to implement two methods:

### searchableAs

This method returns a list of indexes the record should be listed under.

```php
public static function searchableAs(): array
{
    return ['global_search'];
}
```

### toSearchableArray

This method returns the payload in the form of array with the key representing corresponding index.

```php
public function toSearchableArray(): array
{
    return [
        'global_search' => [
            'type' => 'book',
            'id' => $this->getSearchKey(),
            'name' => $this->name,
            'author' => $this->author,
            'description' => $this->description,
            'created_at' => $this->created_at->timestamp,
        ],
    ];
}
```

You can also overwrite the `shouldBeSearchable` method to indicate whether the record should be indexed.

## Register search request parameters for your given implementation

If you are using the built-in controller with the `SearchRequest`, within your `AppServiceProvider::boot` method add all
relevant validation
rules for a given index.
These will be used with the `Indexer::search` method - if you are using different approach for the search, you can
ignore these.
By default, only `index` and `params` (as array) are validated - whatever you define via this method will be merged to
it.

```php
\SebastianSulinski\Search\Facades\Search::validation('global_search', fn (\Illuminate\Foundation\Http\FormRequest $request) => [
    'params.q' => [
        'nullable',
        'string',
    ],
    'params.query_by' => [
        'required',
        'string',
    ],
    'params.highlight_fields' => [
        'nullable',
        'string',
    ],
    'params.facet_by' => [
        'nullable',
        'string',
    ],
    'params.filter_by' => [
        'nullable',
        'string',
    ],
    'params.page' => [
        'nullable',
        'integer',
    ],
    'params.per_page' => [
        'nullable',
        'integer',
    ],
    'params.sort_by' => [
        'nullable',
        'string',
    ],
]);
```

## Disable default routes

To disable default routes add the following to your `AppServiceProvider::register` method:

```php
\SebastianSulinski\Search\Facades\Search::withoutRoutes();
```

## Import records

The following command will import all records for all indexes using default queue.

```bash
php artisan app:import-search
```

You can also specify the index you'd like to import:

```bash
php artisan app:import-search global_search
```

## Remove index and all records

To remove the index and all its records use the following command:

```bash
php artisan app:purge-search global_search
```

## Contributions

Contributions are welcome, but please make sure your code contains all necessary bells and whistles - pint it etc.