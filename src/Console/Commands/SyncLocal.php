<?php

namespace Khaleejinfotech\LaravelDbSync\Console\Commands;

use Illuminate\Console\Command;
use Illuminate\Contracts\Bus\Dispatcher;
use Khaleejinfotech\LaravelDbSync\Jobs\SyncLocalJob;
use Khaleejinfotech\LaravelDbSync\Models\Sync;

class SyncLocal extends Command
{
    /**
     * The name and signature of the console command.
     *
     * @var string
     */
    protected $signature = 'sync:local';

    /**
     * The console command description.
     *
     * @var string
     */
    protected $description = 'Sync local database records to remote targets';

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
        $records = Sync::where('job_id')
            ->where(function ($where) {
                $where->where('synced')->orWhere('synced', 0);
            });
        if ($records->count() > 0)
            foreach ($records->get() as $record) {
                $jobID = app(Dispatcher::class)->dispatch(new SyncLocalJob($record));
                $record->update(['job_id' => $jobID]);
                $this->info("Job created successfully #{$jobID}");
            }
        else
            $this->info("Nothing to sync!!");
    }
}
