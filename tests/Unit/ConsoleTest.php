<?php

namespace SebastianSulinski\SearchTests\Unit;

use Illuminate\Foundation\Testing\RefreshDatabase;
use Illuminate\Support\Facades\Artisan;
use Mockery\MockInterface;
use PHPUnit\Framework\Attributes\Test;
use SebastianSulinski\Search\Indexer;
use SebastianSulinski\SearchTests\BaseTest;

class ConsoleTest extends BaseTest
{
    use RefreshDatabase;

    #[Test]
    public function imports_documents(): void
    {
        $this->partialMock(Indexer::class, function (MockInterface $mock) {
            $mock->shouldReceive('import')->once();
        });

        Artisan::call('app:import-search');
    }

    #[Test]
    public function purges_documents(): void
    {
        $this->partialMock(Indexer::class, function (MockInterface $mock) {
            $mock->shouldReceive('purge')->with('global_search')->once();
        });

        Artisan::call('app:purge-search', ['index' => 'global_search']);
    }
}
