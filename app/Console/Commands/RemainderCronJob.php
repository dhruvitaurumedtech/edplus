<?php

namespace App\Console\Commands;

use App\Models\Remainder_model;
use Illuminate\Console\Command;
use PDO;

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
