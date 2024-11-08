<?php

namespace SebastianSulinski\Search;

use Illuminate\Support\Collection;
use Illuminate\Support\Facades\Facade;
use SebastianSulinski\Search\Filter\Response;

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
     * {@inheritDoc}
     */
    protected static function getFacadeAccessor(): string
    {
        return SearchManager::class;
    }
}
