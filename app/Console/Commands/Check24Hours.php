<?php

namespace App\Console\Commands;

use App\Models\VideoAssignToBatch;
use Carbon\Carbon;
use Illuminate\Console\Command;

class Check24Hours extends Command
{
    /**
     * The name and signature of the console command.
     *
     * @var string
     */
    protected $signature = 'check:24hours';

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
        // Get the current time minus 24 hours
        $cutoffTime = Carbon::now()->subHours(24)->toDateTimeString();

        // Delete records older than 24 hours
        VideoAssignToBatch::where('created_at', '<', $cutoffTime)->delete();
    }
}
