<?php

namespace App\Commands;

use Illuminate\Console\Scheduling\Schedule;
use LaravelZero\Framework\Commands\Command;

use App\Library\Services\Mailbox;

class Clean extends Command
{
    /**
     * The signature of the command.
     *
     * @var string
     */
    protected $signature = 'clean 
                            {--C|criteria}
                            {--M|mailbox}';

    /**
     * The description of the command.
     *
     * @var string
     */
    protected $description = 'Clean all emails from a sender';

    /**
     * Execute the console command.
     *
     * @return mixed
     */
    public function handle()
    {
        $criteria = ($this->option('criteria') ? $this->option('criteria') : $this->ask('Email to search?'));
        $mailbox = ($this->option('mailbox') ? $this->option('mailbox') : $this->ask('Folder?'));
        $conn = new Mailbox();
        $messages = $conn->searchFrom($mailbox, $criteria);
        $bar = $this->output->createProgressBar(count($messages));
        $bar->setOverwrite(true);
        foreach ($messages as $message) {
            $message->delete();
            $bar->advance();
        }
        $bar->finish();
        $conn->deleteAll();
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
