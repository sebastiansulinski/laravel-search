# Register search request parameters for your given implementation

Within your `AppServiceProvider::boot` method add all relevant validation rules.
By default, request will only validate `index` and `params` and will merge the ones from the macro with it.

```php
\App\Search\Requests\SearchRequest::macro('global_search', fn (SearchRequest $request) => [
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

# Disable default routes

To disable default routes add the following to your `AppServiceProvider::register` method:

```php
\App\Search\Search::withoutRoutes();
```