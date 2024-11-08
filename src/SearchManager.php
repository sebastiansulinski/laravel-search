<?php

namespace SebastianSulinski\Search;

use Illuminate\Support\Collection;
use Illuminate\Support\Manager;
use SebastianSulinski\Search\Drivers\NullDriver;
use SebastianSulinski\Search\Drivers\Typesense;
use Typesense\Client;

class SearchManager extends Manager
{
    /**
     * {@inheritDoc}
     */
    public function getDefaultDriver(): string
    {
        return $this->config->get('search.default') ?: 'null';
    }

    /**
     * Create typesense driver.
     *
     * @throws \Illuminate\Contracts\Container\BindingResolutionException
     */
    public function createTypesenseDriver(): Indexer
    {
        return new Typesense(
            client: $this->container->make(Client::class),
            models: new Collection($this->config->get('search.models')),
            collections: $this->config->get('search.drivers.typesense.collections')
        );
    }

    /**
     * Create null driver.
     */
    public function createNullDriver(): Indexer
    {
        return new NullDriver;
    }
}
