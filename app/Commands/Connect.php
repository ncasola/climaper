<?php

namespace App\Commands;

use Illuminate\Console\Scheduling\Schedule;
use LaravelZero\Framework\Commands\Command;
use App\Library\Services\Mailbox;

class Connect extends Command
{
    /**
     * The signature of the command.
     *
     * @var string
     */
    protected $signature = 'connect';
    /**
     * The description of the command.
     *
     * @var string
     */
    protected $description = 'Connect to IMAP server so you can late run all fun things';

    /**
     * Execute the console command.
     *
     * @return mixed
     */
    public function handle()
    {
        $a["url"] = $this->ask('What is the URL of imap server?');
        $a["username"] = $this->ask('What is the username of imap server?');
        $a["password"] = $this->ask('What is the password of imap server?');
        $this->task("Trying and saving for later...", function () use ($a) {
            $conn = new Mailbox($a["url"], $a["username"], $a["password"]);
            return true;
        });
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
