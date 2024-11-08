<?php

namespace SebastianSulinski\Search\Drivers;

use Http\Client\Exception as HttpClientException;
use Illuminate\Support\Collection;
use Illuminate\Support\LazyCollection;
use SebastianSulinski\Search\Filter\Paging;
use SebastianSulinski\Search\Filter\RequestParams;
use SebastianSulinski\Search\Filter\Response;
use SebastianSulinski\Search\IndexableDocument;
use SebastianSulinski\Search\Indexer;
use SebastianSulinski\Search\Jobs\ImportSearch;
use Typesense\Client;
use Typesense\Exceptions\ObjectNotFound;
use Typesense\Exceptions\TypesenseClientError;

readonly class Typesense implements Indexer
{
    /**
     * Indexer constructor.
     */
    public function __construct(private Client $client, private Collection $models, private array $collections) {}

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
                    ->documents[$document->getSearchKey()]
                    ->update($payload)
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
     * @throws \Http\Client\Exception
     * @throws \JsonException
     * @throws \Typesense\Exceptions\TypesenseClientError
     */
    public function import(?string $index = null): void
    {
        $indexes = $this->documentIndexes();

        if ($index) {
            $this->importDocuments($indexes[$index], $index);

            return;
        }

        $indexes->each($this->importDocuments(...));
    }

    /**
     * Get all documents grouped by index key.
     */
    private function documentIndexes(): Collection
    {
        return $this->models->reduce($this->groupDocuments(...), new Collection);
    }

    /**
     * Import documents for the given index.
     *
     * @throws \Http\Client\Exception
     * @throws \JsonException
     * @throws \Typesense\Exceptions\TypesenseClientError
     */
    private function importDocuments(array $documents, string $index): void
    {
        foreach ($documents as $document) {
            $this->addBulk($index, $document::searchable($index));
        }
    }

    /**
     * Import documents to collection.
     *
     * @throws \Http\Client\Exception
     * @throws \Typesense\Exceptions\TypesenseClientError
     */
    private function addBulk(string $index, LazyCollection $payload): void
    {
        if (! $this->collection($index)) {
            $this->client->collections->create(
                $this->collections[$index]['schema']
            );
        }

        $payload->chunk(50)->each(
            fn (LazyCollection $chunk) => ImportSearch::dispatch($index, $chunk->toArray())
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
     * Reduce document.
     *
     * @param  class-string  $document
     */
    private function groupDocuments(Collection $result, string $document): Collection
    {
        foreach ($document::searchableAs() as $searchable) {
            $result[$searchable] = array_merge(
                $result[$searchable] ?? [],
                [$document]
            );
        }

        return $result;
    }
}
