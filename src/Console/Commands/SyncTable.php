<?php

namespace Khaleejinfotech\LaravelDbSync\Console\Commands;

use Illuminate\Console\Command;
use Illuminate\Support\Facades\File;

class SyncTable extends Command
{
    /**
     * The name and signature of the console command.
     *
     * @var string
     */
    protected $signature = 'sync:table';

    /**
     * The console command description.
     *
     * @var string
     */
    protected $description = 'Creates sync table migration';

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
        File::copy(dirname(dirname(__DIR__)) . '/Migration/2022_10_10_202746_create_syncs_table.php', database_path('migrations') . '/2022_10_10_202746_create_syncs_table.php');
        $this->info('Migration file created.');
    }
}
