<?php

namespace App\Commands;

use Illuminate\Console\Scheduling\Schedule;
use LaravelZero\Framework\Commands\Command;
use App\Library\Services\Mailbox;
use Illuminate\Support\Facades\DB;

class Loadmails extends Command
{
    /**
     * The signature of the command.
     *
     * @var string
     */
    protected $signature = 'load:mails {mailbox?}';

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
        $conn = new Mailbox();
        $mailbox = ($this->argument('mailbox') ? $this->argument('mailbox') : $this->ask('Folder'));
        $messages = $conn->all($mailbox);
        $bar = $this->output->createProgressBar(count($messages));
        $bar->setOverwrite(true);
        foreach ($messages as $message) {
            try {
                DB::table('mails')->insert( [
                    'mail_from' => $message->getFrom()->getAddress(),
                    'mail_id' => $message->getNumber(),
                    'mailbox' => $mailbox
                ] );
            } catch (Exception $e) {
                continue;
            }
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
