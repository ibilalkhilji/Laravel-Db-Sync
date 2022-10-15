<?php

namespace Khaleejinfotech\LaravelDbSync\Jobs;

use Exception;
use Illuminate\Bus\Queueable;
use Illuminate\Contracts\Queue\ShouldQueue;
use Illuminate\Foundation\Bus\Dispatchable;
use Illuminate\Queue\InteractsWithQueue;
use Illuminate\Queue\SerializesModels;
use Illuminate\Support\Facades\Log;
use Illuminate\Support\Facades\Storage;
use Khaleejinfotech\LaravelDbSync\Events\NoTargetDefined;
use Khaleejinfotech\LaravelDbSync\Events\SyncFailed;
use Khaleejinfotech\LaravelDbSync\Events\SyncSuccess;
use Khaleejinfotech\LaravelDbSync\Events\SyncUploadFailed;
use Khaleejinfotech\LaravelDbSync\Events\SyncUploadSuccess;
use Khaleejinfotech\LaravelDbSync\Models\Sync;

class SyncLocalUploadJob implements ShouldQueue
{
    use Dispatchable, InteractsWithQueue, Queueable, SerializesModels;

    private $tag = 'SyncLocalUpload :: ';
    private $payload;

    /**
     * Create a new job instance.
     *
     * @return void
     */
    public function __construct($payload)
    {
        $this->payload = $payload;
    }

    /**
     * Execute the job.
     *
     * @return void
     * @throws Exception
     */
    public function handle()
    {
        $payLoads = json_decode($this->payload->payload);
        foreach ($payLoads as $payLoad) {
            if (count($payLoad->disks) > 0) {
                try {
                    foreach ($payLoad->disks as $disk) {
                        $localPath = Storage::disk($payLoad->local_disk)->getDriver()->getAdapter()->getPathPrefix();
                        \Storage::disk($disk)
                            ->put($payLoad->remote_path . "/" . $payLoad->fileName,
                                file_get_contents($localPath . $payLoad->local_path . "/" . $payLoad->fileName));
                    }
                } catch (\Exception $exception) {
                    SyncUploadFailed::dispatch($exception->getMessage());
                    throw new Exception($exception);
                }
            } else {
                SyncUploadFailed::dispatch("No disks defined.");
                throw new Exception("No disks defined.");
            }
        }
        SyncUploadSuccess::dispatch();
        Sync::withoutEvents(function () {
            Sync::find($this->payload->id)->update(['synced' => 1]);
        });
    }

    public function failed($exception)
    {
        Log::error($this->tag . $exception->getMessage());
        Sync::withoutEvents(function () {
            Sync::find($this->payload->id)->update(['job_id' => null]);
        });
    }
}
