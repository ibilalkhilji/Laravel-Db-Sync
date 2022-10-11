<?php

namespace Khaleejinfotech\LaravelDbSync\Observers;

use Khaleejinfotech\LaravelDbSync\Models\Sync;

class ModelObserver
{
    /**
     * Handle the model "retrieved" event.
     *
     * @param  $model
     * @return void
     */
    public function retrieved($model)
    {
        //
    }

    /**
     * Handle the model "created" event.
     *
     * @param  $model
     * @return void
     */
    public function created($model)
    {
        Sync::create([
            'model' => get_class($model),
            'payload' => $model->toJson(),
            'action' => 'create',
            'synced' => 0,
        ]);
    }

    /**
     * Handle the model "updated" event.
     *
     * @param  $model
     * @return void
     */
    public function updated($model)
    {
        Sync::create([
            'model' => get_class($model),
            'payload' => $model->toJson(),
            'action' => 'update',
            'synced' => 0,
        ]);
    }
}
