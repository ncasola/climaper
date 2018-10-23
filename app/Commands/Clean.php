<?php

namespace App\Commands;

use Illuminate\Console\Scheduling\Schedule;
use LaravelZero\Framework\Commands\Command;
use Jackiedo\DotenvEditor\Facades\DotenvEditor;
use Ddeboer\Imap\Server;
use Ddeboer\Imap\SearchExpression;
use Ddeboer\Imap\Search\Email\From;

class Clean extends Command
{
    /**
     * The signature of the command.
     *
     * @var string
     */
    protected $signature = 'clean';

    /**
     * The description of the command.
     *
     * @var string
     */
    protected $description = 'Clean all junk emails';

    /**
     * Execute the console command.
     *
     * @return mixed
     */
    public function handle()
    {
        $keys = DotenvEditor::getKeys();
        $server = new Server($keys["IMAP_URL"]["value"]);
        $connection = $server->authenticate($keys["IMAP_USERNAME"]["value"], $keys["IMAP_PASSWORD"]["value"]);
        $mailbox = $connection->getMailbox('Archive');
        $search = new SearchExpression();
        $search->addCondition(new From('do-not-reply@imdb.com'));
        $messages = $mailbox->getMessages($search);
        $bar = $this->output->createProgressBar(count($messages));
        $bar->setOverwrite(true);
        foreach ($messages as $message) {
            $message->delete();
            $bar->advance();
        }
        $bar->finish();
        $connection->expunge();
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
