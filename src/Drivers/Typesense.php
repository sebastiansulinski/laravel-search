<?php

namespace SebastianSulinski\Search\Drivers;

use Http\Client\Exception as HttpClientException;
use Illuminate\Support\Collection;
use Illuminate\Support\LazyCollection;
use Illuminate\Support\Str;
use SebastianSulinski\Search\Filter\Paging;
use SebastianSulinski\Search\Filter\RequestParams;
use SebastianSulinski\Search\Filter\Response;
use SebastianSulinski\Search\IndexableDocument;
use SebastianSulinski\Search\Indexer;
use SebastianSulinski\Search\Jobs\ImportSearch;
use Typesense\Client;
use Typesense\Exceptions\ObjectNotFound;
use Typesense\Exceptions\TypesenseClientError;

class Typesense implements Indexer
{
    use DriverHelpers;

    /**
     * Indexer constructor.
     */
    public function __construct(
        private readonly Client $client,
        private readonly Collection $models,
        private readonly array $collections,
        private readonly bool $removeUndefinedCollections = false
    ) {}

    /**
     * {@inheritDoc}
     *
     * @throws TypesenseClientError|HttpClientException
     */
    public function initialise(): void
    {
        $existing = collect($this->client->collections->retrieve())
            ->pluck('name');

        foreach ($this->collections as $collection) {
            $schema = $collection['schema'];

            if ($existing->contains($schema['name'])) {
                continue;
            }

            $this->client->collections->create($schema);
        }

        $this->removeUndefinedCollections($existing);
    }

    /**
     * {@inheritDoc}
     *
     * @throws TypesenseClientError|HttpClientException
     */
    public function create(IndexableDocument $document): bool
    {
        foreach ($document->toSearchableArray() as $index => $payload) {
            if (! $this->client->collections[$index]->documents->create($payload)) {
                return false;
            }
        }

        return true;
    }

    /**
     * Remove undefined collections.
     *
     * @throws TypesenseClientError|HttpClientException
     */
    private function removeUndefinedCollections(Collection $names): void
    {
        if (! $this->removeUndefinedCollections) {
            return;
        }

        foreach ($names as $name) {
            if (array_key_exists($name, $this->collections)) {
                continue;
            }

            $this->client->collections[$name]->delete();
        }
    }

    /**
     * {@inheritDoc}
     *
     * @throws TypesenseClientError|HttpClientException
     */
    public function delete(IndexableDocument $document): bool
    {
        foreach ($document->searchableAs() as $index) {
            if (
                ! $this->client->collections[$index]
                    ->documents[$document->getSearchKey()]->delete()
            ) {
                return false;
            }
        }

        return true;
    }

    /**
     * {@inheritDoc}
     *
     * @throws TypesenseClientError|HttpClientException
     */
    public function update(IndexableDocument $document): bool
    {
        foreach ($document->toSearchableArray() as $index => $payload) {
            if (
                ! $this->client->collections[$index]
                    ->documents
                    ->upsert($payload)
            ) {
                return false;
            }
        }

        return true;
    }

    /**
     * {@inheritDoc}
     *
     * @throws TypesenseClientError|HttpClientException
     */
    public function purge(string $index): void
    {
        $this->client->collections[$index]->delete();
    }

    /**
     * {@inheritDoc}
     *
     * @throws TypesenseClientError|HttpClientException
     */
    public function indexes(): Collection
    {
        return new Collection($this->client->collections->retrieve());
    }

    /**
     * {@inheritDoc}
     */
    public function documents(string $index): Collection
    {
        return new Collection($this->client->collections[$index]
            ->documents->search([
                'q' => '*',
                'per_page' => 250,
            ]));
    }

    /**
     * {@inheritDoc}
     *
     * @throws TypesenseClientError|HttpClientException
     */
    public function search(string $index, array $params): Response
    {
        $default = $this->collections[$index]['search-parameters'];

        $response = $this->client->collections[$index]
            ->documents
            ->search(array_merge(
                [
                    'query_by' => $default['query_by'],
                ],
                $params
            ));

        return new Response(
            records: new Collection($response['hits']),
            paging: new Paging(
                totalRecords: $response['out_of'],
                matchingRecords: $response['found'],
                currentPage: $response['page'],
                perPage: $response['request_params']['per_page'],
            ),
            params: new RequestParams(
                index: $response['request_params']['collection_name'],
                query: $response['request_params']['q'],
            )
        );
    }

    /**
     * {@inheritDoc}
     *
     * @throws TypesenseClientError
     * @throws \JsonException|HttpClientException
     */
    public function importChunk(?string $index, array $documents): void
    {
        $this->client
            ->collections[$index]
            ->documents
            ->import($documents, ['action' => 'create']);
    }

    /**
     * {@inheritDoc}
     *
     * @throws TypesenseClientError|HttpClientException
     */
    public function export(string $index): array
    {
        return json_decode('['.Str::replace(
            PHP_EOL, ',', $this->client->collections[$index]->documents->export()
        ).']');
    }

    /**
     * {@inheritDoc}
     *
     * @throws \Http\Client\Exception
     * @throws \Typesense\Exceptions\TypesenseClientError
     */
    protected function importDocuments(string $index, LazyCollection $payload): void
    {
        if (! $this->collection($index)) {
            $this->client->collections->create(
                $this->collections[$index]['schema']
            );
        }

        $payload->chunk(50)->each(
            fn (LazyCollection $chunk) => ImportSearch::dispatch(
                $index, $chunk->toArray()
            )
        );
    }

    /**
     * Get collection.
     *
     * @throws \Http\Client\Exception
     * @throws \Typesense\Exceptions\TypesenseClientError
     */
    private function collection(string $index): ?array
    {
        try {
            return $this->client->collections[$index]->retrieve();
        } catch (ObjectNotFound) {
            return null;
        }
    }
}
