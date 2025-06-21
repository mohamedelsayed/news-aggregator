<?php

namespace App\Console\Commands;

use App\Services\GuardianFetcher;
use App\Services\NewsApiFetcher;
use App\Services\NewYorkTimesFetcher;
use Illuminate\Console\Command;

class FetchArticles extends Command
{
    /**
     * The name and signature of the console command.
     *
     * @var string
     */
    protected $signature = 'articles:fetch';

    /**
     * The console command description.
     *
     * @var string
     */
    protected $description = 'Command description';

    /**
     * Execute the console command.
     */
    public function handle()
    {
        $this->info('Fetching from NewsAPI...');
        app(NewsApiFetcher::class)->fetchAndStore();

        $this->info('Fetching from Guardian...');
        app(GuardianFetcher::class)->fetchAndStore();

        $this->info('Fetching from NYT...');
        app(NewYorkTimesFetcher::class)->fetchAndStore();

        $this->info('All done!');

    }
}
