<?php

namespace App\Commands;

use Illuminate\Console\Scheduling\Schedule;
use LaravelZero\Framework\Commands\Command;
use Ddeboer\Imap\Server;
use Jackiedo\DotenvEditor\Facades\DotenvEditor;

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
        $this->task("Conecting to imap...", function () use ($a) {
            $server = new Server($a["url"]);
            $connection = $server->authenticate($a["username"], $a["password"]);
            return true;
        });
        $this->task("Saving connect info for later", function () use ($a) {
            $file = DotenvEditor::load();
            $file->setKey('IMAP_URL', $a["url"]);
            $file->setKey('IMAP_USERNAME', $a["username"]);
            $file->setKey('IMAP_PASSWORD', $a["password"]);
            $file->save();
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
