<?php

namespace SebastianSulinski\Search\Drivers;

use Illuminate\Support\Collection;
use Illuminate\Support\LazyCollection;

trait DriverHelpers
{
    /**
     * {@inheritDoc}
     *
     * @throws \Http\Client\Exception
     * @throws \Typesense\Exceptions\TypesenseClientError
     */
    public function import(?string $index = null): void
    {
        $models = $this->modelsByIndex();

        if ($index) {
            $this->importModels($models[$index], $index);

            return;
        }

        $models->each($this->importModels(...));
    }

    /**
     * Get all documents grouped by index key.
     */
    protected function modelsByIndex(): Collection
    {
        return $this->models->reduce($this->groupModelsByIndex(...), new Collection);
    }

    /**
     * Import documents by model for the given index.
     *
     * @param  array<class-string>  $models
     *
     * @throws \Http\Client\Exception
     * @throws \Typesense\Exceptions\TypesenseClientError
     */
    protected function importModels(array $models, string $index): void
    {
        foreach ($models as $model) {
            $this->importDocuments($index, $model::searchable($index));
        }
    }

    /**
     * Import documents by index.
     */
    abstract protected function importDocuments(string $index, LazyCollection $payload): void;

    /**
     * Reduce document.
     *
     * @param  class-string  $model
     */
    protected function groupModelsByIndex(Collection $result, string $model): Collection
    {
        foreach ($model::searchableAs() as $index) {
            $result[$index] = array_merge(
                $result[$index] ?? [],
                [$model]
            );
        }

        return $result;
    }
}
