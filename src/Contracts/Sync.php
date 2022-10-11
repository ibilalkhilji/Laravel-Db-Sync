<?php

namespace Khaleejinfotech\LaravelDbSync\Contracts;

use Exception;

trait Sync
{
    /**
     * @throws Exception
     */
    public static function bootSync()
    {
        $observerClass = config('laravel-db-sync.observer_class');

        if ($observerClass == null || $observerClass == '')
            throw new Exception("Observer class not defined");

        if (!in_array(get_called_class(), config('laravel-db-sync.ignore_models')))
            static::observe(new $observerClass);
    }

}
