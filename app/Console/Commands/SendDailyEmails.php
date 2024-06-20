<?php

namespace App\Console\Commands;

use Illuminate\Console\Command;

class SendDailyEmails extends Command
{
    /**
     * The name and signature of the console command.
     *
     * @var string
     */
    protected $signature = 'app:send-daily-emails';

    protected $description = 'Send daily emails to users';

    public function __construct()
    {
        parent::__construct();
    }

    public function handle()
    {
        // Logic to send emails
        $this->info('Daily emails sent successfully.');
    }
}
