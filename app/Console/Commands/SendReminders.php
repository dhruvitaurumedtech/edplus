<?php

namespace App\Console\Commands;

use App\Models\User;
use Carbon\Carbon;
use Illuminate\Console\Command;
use Illuminate\Support\Facades\Mail;

class SendReminders extends Command
{
    /**
     * The name and signature of the console command.
     *
     * @var string
     */
    protected $signature = 'app:send-reminders';

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
        
            Mail::raw("Happy Birthday,!", function ($message) {
                $message->to('iitjeeneetexam@gmail.com')
                        ->subject('Happy Birthday!');
            });

            $this->info("Birthday reminder sent to: iitjeeneetexam@gmail.com");
        
    }
}
