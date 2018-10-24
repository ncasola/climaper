<?php

namespace App\Commands;

use Illuminate\Console\Scheduling\Schedule;
use LaravelZero\Framework\Commands\Command;
use DB;

class Loadmails extends Command
{
    /**
     * The signature of the command.
     *
     * @var string
     */
    protected $signature = 'load:mails {--M|mailbox}';

    /**
     * The description of the command.
     *
     * @var string
     */
    protected $description = 'Import all mails from folder to database';

    /**
     * Execute the console command.
     *
     * @return mixed
     */
    public function handle()
    {
        $mailbox = ($this->option('mailbox') ? $this->option('mailbox') : $this->ask('Folder?'));
        $conn = new Mailbox();
        $messages = $conn->all($mailbox);
        $bar = $this->output->createProgressBar(count($messages));
        $bar->setOverwrite(true);
        foreach ($messages as $message) {
            DB::table('mails')->insert( [
                'mail_from' => $message->getFrom(),
                'mail_id' => $message->getId(),
                'mailbox' => $mailbox
            ] );
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
