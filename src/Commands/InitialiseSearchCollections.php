<?php

namespace SebastianSulinski\Search\Commands;

use Illuminate\Console\Command;
use SebastianSulinski\Search\Indexer;

class InitialiseSearchCollections extends Command
{
    /**
     * The name and signature of the console command.
     *
     * @var string
     */
    protected $signature = 'app:initialise-search';

    /**
     * The console command description.
     *
     * @var string
     */
    protected $description = 'Initialise search collections.';

    /**
     * Execute the console command.
     */
    public function handle(Indexer $indexer): void
    {
        $indexer->initialise();
    }
}
