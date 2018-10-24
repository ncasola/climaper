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
    protected $signature = 'connect {info?* : In this format example@gmail.com pass imap.gmail.com}';
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
        $ifKey = \DotenvEditor::keyExists('IMAP_URL');
        if($ifKey) {
            $this->info("No need to connect, you are already connect-it");
            exit();
        }elseif($this->argument("info")) {
            $info = $this->argument();
        } else {
            $this->info("Need the connect info in the follow format: example@gmail.com|pass|imap.gmail.com");
            $info = explode("|", $this->ask("Connect info"));
        }
        list($username, $password, $url) = $info;
        $this->task("Trying conn and saving for later...", function () use ($url, $username, $password) {
            $conn = new Mailbox($url, $username, $password);
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
