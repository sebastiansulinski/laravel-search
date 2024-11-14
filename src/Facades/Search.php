<?php

namespace SebastianSulinski\Search\Facades;

use Illuminate\Support\Collection;
use Illuminate\Support\Facades\Facade;
use SebastianSulinski\Search\Filter\Response;
use SebastianSulinski\Search\IndexableDocument;
use SebastianSulinski\Search\Indexer;
use SebastianSulinski\Search\Requests\SearchRequest;
use SebastianSulinski\Search\SearchManager;

/**
 * @method static bool create(IndexableDocument $document)
 * @method static bool update(IndexableDocument $document)
 * @method static bool delete(IndexableDocument $document)
 * @method static void import(?string $index = null)
 * @method static void importChunk(?string $index, array $documents)
 * @method static void purge(string $index)
 * @method static Collection indexes()
 * @method static Collection documents(string $index)
 * @method static Response search(string $index, array $params)
 * @method static Indexer driver($driver = null)
 *
 * @see \SebastianSulinski\Search\Indexer
 */
class Search extends Facade
{
    public static bool $loadRoutes = true;

    /**
     * Disable default routes.
     */
    public static function withoutRoutes(): void
    {
        static::$loadRoutes = false;
    }

    /**
     * Get a list of all available indexes.
     */
    public static function availableIndexes(): array
    {
        return collect(config('search.models', []))
            ->flatMap(fn (string $model) => $model::searchableAs())
            ->unique()
            ->values()
            ->toArray();
    }

    /**
     * Add custom validation rules for a given index.
     */
    public static function validation(string $index, callable|object|array $rules): void
    {
        SearchRequest::macro($index, $rules);
    }

    /**
     * {@inheritDoc}
     */
    protected static function getFacadeAccessor(): string
    {
        return SearchManager::class;
    }
}
