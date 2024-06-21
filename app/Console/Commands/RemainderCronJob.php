<?php

namespace App\Console\Commands;

use Illuminate\Console\Command;

class RemainderCronJob extends Command
{
    /**
     * The name and signature of the console command.
     *
     * @var string
     */
    protected $signature = 'app:remainder-cron-job';

    /**
     * The console command description.
     *
     * @var string
     */
    protected $description = 'Command description';

    /**
     * Execute the console command.
     */
    public function handle()
    {
        \Log::info('SampleCronJob is running successfully.');
    }
}
