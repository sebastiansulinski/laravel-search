<?php

namespace SebastianSulinski\SearchTests;

use Orchestra\Testbench\Concerns\WithWorkbench;
use Orchestra\Testbench\TestCase;

use function Orchestra\Testbench\workbench_path;

class BaseTest extends TestCase
{
    use WithWorkbench;

    /**
     * Define database migrations.
     */
    protected function defineDatabaseMigrations(): void
    {
        $this->loadMigrationsFrom(
            workbench_path('database/migrations')
        );
    }
}
