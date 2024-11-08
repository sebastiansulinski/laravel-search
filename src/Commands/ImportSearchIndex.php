<?php

namespace SebastianSulinski\Search\Commands;

use Illuminate\Console\Command;
use SebastianSulinski\Search\Indexer;

class ImportSearchIndex extends Command
{
    /**
     * The name and signature of the console command.
     *
     * @var string
     */
    protected $signature = 'app:import-search  {index?}';

    /**
     * The console command description.
     *
     * @var string
     */
    protected $description = 'Bulk import search.';

    /**
     * Execute the console command.
     */
    public function handle(Indexer $indexer): void
    {
        $indexer->import($this->argument('index'));
    }
}
