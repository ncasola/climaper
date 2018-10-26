<?php

namespace App\Commands;

use Illuminate\Console\Scheduling\Schedule;
use LaravelZero\Framework\Commands\Command;
use App\Library\Services\Mailbox;
use Illuminate\Support\Facades\DB;

class Wipe extends Command
{
    /**
     * The signature of the command.
     *
     * @var string
     */
    protected $signature = 'wipe {file?} {mailbox?}';

    /**
     * The description of the command.
     *
     * @var string
     */
    protected $description = 'Wipe a list of emails from a mailbox';

    /**
     * Execute the console command.
     *
     * @return mixed
     */
    public function handle()
    {
        $conn = new Mailbox();
        $file = ($this->argument('file') ? $this->argument('file') : storage_path("emails.txt"));
        $mailbox = ($this->argument('mailbox') ? $this->argument('mailbox') : $this->ask('Folder'));
        $list = file($file, FILE_IGNORE_NEW_LINES);
        $messages = $conn->searchList($mailbox, $list);
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
