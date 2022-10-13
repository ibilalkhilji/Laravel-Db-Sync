<?php

namespace Khaleejinfotech\LaravelDbSync\Contracts;

use DateTimeInterface;
use Exception;

trait Syncable
{
    /**
     * @throws Exception
     */
    public static function bootSyncable()
    {
        $observerClass = config('laravel-db-sync.observer_class');

        if ($observerClass == null || $observerClass == '')
            throw new Exception("Observer class not defined");

        if (!in_array(get_called_class(), config('laravel-db-sync.ignore_models')))
            static::observe(new $observerClass);
    }

    protected function serializeDate(DateTimeInterface $date): string
    {
        return $date->format('Y-m-d H:i:s');
    }

}
