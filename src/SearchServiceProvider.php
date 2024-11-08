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
     *
     * @throws \Illuminate\Contracts\Container\BindingResolutionException
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

        $this->registerObserver();
    }

    /**
     * Register observer.
     *
     * @throws \Illuminate\Contracts\Container\BindingResolutionException
     */
    private function registerObserver(): void
    {
        foreach ($this->app->make('config')->get('search.models') as $model) {
            $model::observe($this->app->make(SearchObserver::class));
        }
    }

    /**
     * Register bindings.
     */
    public function register(): void
    {
        $this->app->singleton(
            Indexer::class, fn (Application $app) => $app->make(SearchManager::class)->driver()
        );

        $this->app->bind(
            Client::class, fn (Application $app) => new Client(
                $app->make('config')->get('services.typesense')
            )
        );

        $this->app->bind(SearchObserver::class, function (Application $app) {
            return new SearchObserver($app->make(Indexer::class));
        });
    }
}
