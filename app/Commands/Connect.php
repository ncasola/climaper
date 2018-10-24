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
    protected $signature = 'connect
                            {--R|url}
                            {--U|username}
                            {--P|password}';
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
            $this->info("No need to connect, you are already connectit");
            exit();
        }elseif($this->option("url")) {
            $info = $this->options();
        } else {
            $this->info("Need the connect info in the follow format: example@gamilcom|pass|imap.provider.com");
            $info = explode("|", $this->ask("Connect info"));
        }
        list($url, $username, $password) = $info;
        $this->task("Trying conn and saving for later...", function () use ($a) {
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
