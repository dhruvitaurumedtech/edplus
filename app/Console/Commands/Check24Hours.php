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
    protected $signature = 'app:check24-hours';

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
        VideoAssignToBatch::where('created_at', '<', Carbon::now()->subHours(24)->toDateTimeString())->delete();  
    }
}
