<?php

namespace Khaleejinfotech\LaravelDbSync\Observers;

use Khaleejinfotech\LaravelDbSync\Models\Sync;

class ModelObserver
{
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
            'action' => Sync::ACTION_CREATE,
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
            'action' => Sync::ACTION_UPDATE,
            'synced' => 0,
        ]);

        if (count(config('laravel-db-sync.has_uploads')) > 0)
            if (\Arr::has(config('laravel-db-sync.has_uploads'), get_class($model))) {
                try {
                    if (count(config('laravel-db-sync.has_uploads')[get_class($model)]) > 0) {
                        foreach (config('laravel-db-sync.has_uploads')[get_class($model)] as $index => $item) {
                            $payload[$index]['fileName'] = $model->{$item['column_name']};
                            $payload[$index]['remote_path'] = $item['upload_path_remote'];
                            $payload[$index]['local_path'] = $item['upload_path_local'];
                            $payload[$index]['disks'] = $item['remote_disks'];
                        }
                        Sync::create([
                            'model' => get_class($model),
                            'payload' => json_encode($payload),
                            'action' => Sync::ACTION_UPLOAD,
                            'synced' => 0
                        ]);
                    } else throw new \Exception("Payload cannot be empty");
                } catch (\Exception $exception) {
                    \Log::error("ModelObserver:: " . $exception);
                }
            }
    }
}
