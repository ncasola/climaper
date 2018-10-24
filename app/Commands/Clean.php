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
                            {criteria?}
                            {mailbox?}';

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
        $conn = new Mailbox();
        $criteria = ($this->argument('criteria') ? $this->argument('criteria') : $this->ask('Email to search'));
        $mailbox = ($this->argument('mailbox') ? $this->argument('mailbox') : $this->ask('Folder'));
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
