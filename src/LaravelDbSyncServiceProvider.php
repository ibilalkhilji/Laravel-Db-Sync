<?php

namespace Khaleejinfotech\LaravelDbSync;

use Illuminate\Support\ServiceProvider;
use Khaleejinfotech\LaravelDbSync\Console\Commands\SyncLocal;
use Khaleejinfotech\LaravelDbSync\Console\Commands\SyncRemote;
use Khaleejinfotech\LaravelDbSync\Console\Commands\SyncTable;

class LaravelDbSyncServiceProvider extends ServiceProvider
{
    public function boot()
    {
        $this->publishes([
            __DIR__ . '/../config/laravel-db-sync.php' => config_path('laravel-db-sync.php')
        ], 'laravel-db-sync');

        if ($this->app->runningInConsole()) {
            $this->commands([
                SyncTable::class,
                SyncLocal::class,
                SyncRemote::class
            ]);
        }

        /*
         * Register the service provider for the dependency.
         */
        $this->app->register('Khaleejinfotech\LaravelDbSync\LaravelDbSyncServiceProvider');
    }

}
