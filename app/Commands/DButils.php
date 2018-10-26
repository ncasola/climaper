<?php

namespace App\Commands;

use Illuminate\Console\Scheduling\Schedule;
use Illuminate\Support\Facades\Storage;
use LaravelZero\Framework\Commands\Command;
use App\Library\Services\Mailbox;
use Illuminate\Support\Facades\DB;

class DButils extends Command
{
    /**
     * The signature of the command.
     *
     * @var string
     */
    protected $signature = 'db {order} {mailbox?} {--file}';

    /**
     * The description of the command.
     *
     * @var string
     */
    protected $description = 'Works with the database of emails';

    /**
     * Execute the console command.
     *
     * @return mixed
     */
    public function handle()
    {
        $conn = new Mailbox();
        $mailbox = ($this->argument('mailbox') ? $this->argument('mailbox') : $this->ask('Folder'));
        $order = $this->argument('order');
        switch ($order) {
            case 'load':
                $messages = $conn->all($mailbox);
                $bar = $this->output->createProgressBar(count($messages));
                $bar->setOverwrite(true);
                DB::table('mails')->truncate();
                foreach ($messages as $message) {
                    $from = $message->getFrom();
                    if(!$from) { continue; }
                        DB::table('mails')->insert( [
                            'mail_from' => $from->getAddress(),
                            'mail_id' => $message->getNumber(),
                            'mailbox' => $mailbox
                        ] );
                    $bar->advance();
                }
                $bar->finish(); 
                break;
            case 'view':
                $big = DB::table('mails')
                    ->select(DB::raw('count(id) as cuenta, mail_from'))
                    ->groupBy('mail_from')
                    ->where('mailbox', '=', $mailbox)
                    ->orderBy('cuenta', 'desc')
                    ->get();
                $array = json_decode(json_encode($big), true);
                $this->table(['Q', 'FROM'], $array);
                if($this->option('file')) {
                    $data = implode(PHP_EOL, array_column($array, 'mail_from'));
                    Storage::put('emails.txt', $data);
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
