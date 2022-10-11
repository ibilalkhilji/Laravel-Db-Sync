<?php

namespace Khaleejinfotech\LaravelDbSync\Jobs;


use Illuminate\Bus\Queueable;
use Illuminate\Contracts\Queue\ShouldQueue;
use Illuminate\Foundation\Bus\Dispatchable;
use Illuminate\Queue\InteractsWithQueue;
use Illuminate\Queue\SerializesModels;
use Khaleejinfotech\LaravelDbSync\Models\Sync;

class SyncLocalJob implements ShouldQueue
{
    use Dispatchable, InteractsWithQueue, Queueable, SerializesModels;

    private $model;

    /**
     * Create a new job instance.
     *
     * @return void
     */
    public function __construct($model)
    {
        $this->model = $model;
    }

    /**
     * Execute the job.
     * This will create/update records from the local server to remote targets/servers.
     * @return void
     */
    public function handle()
    {
        $model = new $this->model->model; // Creating model class
        $model::withoutEvents(function () use ($model) {
            $payLoad = json_decode($this->model->payload, true);
            if ($this->model->action == 'create') {
                if (\Arr::exists($payLoad, 'id')) unset($payLoad['id']);
                $model->create($payLoad);
            } elseif ($this->model->action == 'update') {
                if (\Arr::exists($payLoad, 'id')) {
                    $recordID = $payLoad['id'];
                    unset($payLoad['id']);

                    foreach (config('app.targets') as $target) {
                        $record = $model::on($target)->find($recordID);
                        if ($record->exists()) $record->update($payLoad);
                    }
                }
            }
        });

        Sync::withoutEvents(function () {
            Sync::find($this->model->id)->update(['synced' => 1]);
        });
    }
}