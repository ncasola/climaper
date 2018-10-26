<?php

namespace App\Commands;

use Illuminate\Console\Scheduling\Schedule;
use LaravelZero\Framework\Commands\Command;
use App\Library\Services\Mailbox;

class Mbutils extends Command
{
    /**
     * The signature of the command.
     *
     * @var string
     */
    protected $signature = 'mbutils {--show}';

    /**
     * The description of the command.
     *
     * @var string
     */
    protected $description = 'Utils for mailboxes';

    /**
     * Execute the console command.
     *
     * @return mixed
     */
    public function handle()
    {
        $conn = new Mailbox();
        if($this->option('show')) {
            $mailboxes = $conn->getMailboxes();
            foreach ($mailboxes as $mailbox) {
                // Skip container-only mailboxes
                // @see https://secure.php.net/manual/en/function.imap-getmailboxes.php
                if ($mailbox->getAttributes() & \LATT_NOSELECT) {
                    continue;
                }
                // $mailbox is instance of \Ddeboer\Imap\Mailbox
                printf('Mailbox "%s" has %s messages' . PHP_EOL, $mailbox->getName(), $mailbox->count());
            }
        }
    }

    /**
     * Define the command's schedule.
     *
     * @param  \Illuminate\Console\Scheduling\Schedule $schedule
     * @return void
     */
    public function schedule(Schedule $schedule): void
    {
        // $schedule->command(static::class)->everyMinute();
    }
}
