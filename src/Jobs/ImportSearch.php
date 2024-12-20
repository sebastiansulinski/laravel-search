<?php

namespace SebastianSulinski\Search\Jobs;

use Illuminate\Contracts\Queue\ShouldQueue;
use Illuminate\Foundation\Queue\Queueable;
use SebastianSulinski\Search\Indexer;

class ImportSearch implements ShouldQueue
{
    use Queueable;

    /**
     * Create a new job instance.
     */
    public function __construct(
        private readonly string $index,
        private readonly array $documents
    ) {
        $config = config('search.queue');

        $this->connection = $config['connection'];
        $this->queue = $config['queue'];
    }

    /**
     * Execute the job.
     */
    public function handle(Indexer $indexer): void
    {
        $indexer->importChunk($this->index, $this->documents);
    }
}
