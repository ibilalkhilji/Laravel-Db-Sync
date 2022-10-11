<?php

namespace Khaleejinfotech\LaravelDbSync\Jobs;

use Illuminate\Bus\Queueable;
use Illuminate\Contracts\Queue\ShouldQueue;
use Illuminate\Foundation\Bus\Dispatchable;
use Illuminate\Queue\InteractsWithQueue;
use Illuminate\Queue\SerializesModels;
use Khaleejinfotech\LaravelDbSync\Models\Sync;

class SyncRemoteJob implements ShouldQueue
{
    use Dispatchable, InteractsWithQueue, Queueable, SerializesModels;

    private $model;
    private $target;

    /**
     * Create a new job instance.
     *
     * @return void
     */
    public function __construct($model, $target)
    {
        $this->model = $model;
        $this->target = $target;
    }

    /**
     * Execute the job.
     * This will create/update records from the remote targets/servers to the local server.
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

                    $record = $model::find($recordID);
                    if ($record->exists()) $record->update($payLoad);
                }
            }
        });

        Sync::withoutEvents(function () {
            Sync::on($this->target)->find($this->model->id)->update(['synced' => 1]);
        });
    }
}
