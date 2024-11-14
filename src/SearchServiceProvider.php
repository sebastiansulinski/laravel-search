<?php

namespace SebastianSulinski\Search;

use Illuminate\Contracts\Foundation\Application;
use Illuminate\Support\ServiceProvider;
use SebastianSulinski\Search\Commands\ImportSearchIndex;
use SebastianSulinski\Search\Commands\PurgeSearchIndex;
use SebastianSulinski\Search\Facades\Search;
use Typesense\Client;

class SearchServiceProvider extends ServiceProvider
{
    /**
     * Bootstrap any package services.
     */
    public function boot(): void
    {
        if ($this->app->runningInConsole()) {
            $this->commands([
                ImportSearchIndex::class,
                PurgeSearchIndex::class,
            ]);
        }

        $this->publishes([
            __DIR__.'/config/search.php' => config_path('search.php'),
        ]);

        $this->mergeConfigFrom(
            __DIR__.'/config/search.php', 'search'
        );

        if (Search::$loadRoutes) {
            $this->loadRoutesFrom(__DIR__.'/routes/search.php');
        }
    }

    /**
     * Register bindings.
     */
    public function register(): void
    {
        $this->app->singleton(
            Indexer::class,
            fn (Application $app) => $app->make(SearchManager::class)->driver()
        );

        $this->app->bind(
            Client::class, fn (Application $app) => new Client(
                $app->make('config')->get('services.typesense')
            )
        );
    }
}
