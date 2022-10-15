<?php

namespace Khaleejinfotech\LaravelDbSync\Jobs;


use Exception;
use Illuminate\Bus\Queueable;
use Illuminate\Contracts\Queue\ShouldQueue;
use Illuminate\Foundation\Bus\Dispatchable;
use Illuminate\Queue\InteractsWithQueue;
use Illuminate\Queue\SerializesModels;
use Illuminate\Support\Facades\Log;
use Khaleejinfotech\LaravelDbSync\Events\NoTargetDefined;
use Khaleejinfotech\LaravelDbSync\Events\SyncFailed;
use Khaleejinfotech\LaravelDbSync\Events\SyncSuccess;
use Khaleejinfotech\LaravelDbSync\Models\Sync;

class SyncLocalJob implements ShouldQueue
{
    use Dispatchable, InteractsWithQueue, Queueable, SerializesModels;

    private $model;
    private $tag = "LaravelDbSync:: ";

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
     * @throws Exception
     */
    public function handle()
    {
        $model = new $this->model->model; // Creating model class
        $model::withoutEvents(function () use ($model) {
            if (count(config('laravel-db-sync.targets')) > 0) {
                try {
                    $payLoad = json_decode($this->model->payload, true);
                    if ($this->model->action == Sync::ACTION_CREATE) {
                        if (\Arr::exists($payLoad, 'id')) unset($payLoad['id']);
                        foreach (config('laravel-db-sync.targets') as $target) {
                            $model::on($target)->create($payLoad);
                        }
                    } elseif ($this->model->action == Sync::ACTION_UPDATE) {
                        if (\Arr::exists($payLoad, 'id')) {
                            $recordID = $payLoad['id'];
                            unset($payLoad['id']);
                            foreach (config('laravel-db-sync.targets') as $target) {
                                $record = $model::on($target)->find($recordID);
                                if ($record->exists()) {
                                    $record->update($payLoad);
                                    if (\Arr::exists($payLoad, 'created_at')) {
                                        $record->created_at = $payLoad['created_at'];
                                        $record->save();
                                    }
                                    if (\Arr::exists($payLoad, 'updated_at')) {
                                        $record->updated_at = $payLoad['updated_at'];
                                        $record->save();
                                    }
                                }
                            }
                        }
                    }
                    SyncSuccess::dispatch();
                    Sync::withoutEvents(function () {
                        Sync::find($this->model->id)->update(['synced' => 1]);
                    });
                } catch (Exception $exception) {
                    SyncFailed::dispatch($exception->getMessage());
                }
            } else {
                NoTargetDefined::dispatch("No targets defined."); //a
                throw new Exception("No targets defined.");
            }
        });
    }

    public function failed($exception)
    {
        Log::error($this->tag . $exception->getMessage());
        Sync::withoutEvents(function () {
            Sync::find($this->model->id)->update(['job_id' => null]);
        });
    }
}
