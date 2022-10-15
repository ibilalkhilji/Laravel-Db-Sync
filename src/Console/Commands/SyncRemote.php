<?php

namespace Khaleejinfotech\LaravelDbSync\Console\Commands;

use Illuminate\Console\Command;
use Illuminate\Contracts\Bus\Dispatcher;
use Khaleejinfotech\LaravelDbSync\Jobs\SyncRemoteJob;
use Khaleejinfotech\LaravelDbSync\Jobs\SyncRemoteUploadJob;
use Khaleejinfotech\LaravelDbSync\Models\Sync;

class SyncRemote extends Command
{
    /**
     * The name and signature of the console command.
     *
     * @var string
     */
    protected $signature = 'sync:remote';

    /**
     * The console command description.
     *
     * @var string
     */
    protected $description = 'Sync remote database records to local server';

    /**
     * Create a new command instance.
     *
     * @return void
     */
    public function __construct()
    {
        parent::__construct();
    }

    /**
     * Execute the console command.
     */
    public function handle()
    {
        $jobID = 0;
        foreach (config('laravel-db-sync.targets') as $target) {
            $records = Sync::on($target)->where('job_id')
                ->where(function ($where) {
                    $where->where('synced')->orWhere('synced', 0);
                });
            if ($records->count() > 0)
                foreach ($records->get() as $record) {
                    if ($record->action == Sync::ACTION_CREATE || $record->action == Sync::ACTION_UPDATE)
                        $jobID = app(Dispatcher::class)->dispatch(new SyncRemoteJob($record, $target));
                    if ($record->action == Sync::ACTION_UPLOAD)
                        $jobID = app(Dispatcher::class)->dispatch(new SyncRemoteUploadJob($record, $target));
                    $record->update(['job_id' => $jobID]);
                    $this->info("Job created successfully #{$jobID}");
                }
            else
                $this->info("Nothing to sync!!");
        }
    }
}
