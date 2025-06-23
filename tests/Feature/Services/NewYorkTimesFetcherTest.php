<?php

namespace Tests\Feature\Services;

use App\DataSource;
use App\Services\NewYorkTimesFetcher;
use Illuminate\Foundation\Testing\RefreshDatabase;
use Illuminate\Support\Facades\Http;
use Tests\TestCase;

class NewYorkTimesFetcherTest extends TestCase
{
    use RefreshDatabase;

    public function test_it_fetches_and_stores_nyt_articles()
    {
        // Mock NYT API response
        Http::fake([
            '*' => Http::response([
                'results' => [
                    [
                        'url' => 'https://nytimes.com/sample-article',
                        'title' => 'Sample NYT Title',
                        'abstract' => 'Sample abstract from NYT.',
                        'byline' => 'By Jane Doe',
                        'section' => 'World',
                        'published_date' => '2025-06-21T12:00:00Z',
                        'multimedia' => [
                            ['url' => 'https://nytimes.com/sample-image.jpg'],
                        ],
                    ],
                ],
            ], 200),
        ]);

        // Call the service
        $fetcher = new NewYorkTimesFetcher;
        $fetcher->fetchAndStore();

        // Assert the article exists in DB
        $this->assertDatabaseHas('articles', [
            'url' => 'https://nytimes.com/sample-article',
            'title' => 'Sample NYT Title',
            'description' => 'Sample abstract from NYT.',
            'content' => 'Sample abstract from NYT.',
            'image_url' => 'https://nytimes.com/sample-image.jpg',
            'source' => DataSource::NEW_YORK_TIMES,
            'author' => 'By Jane Doe',
            'category' => 'World',
            'published_at' => '2025-06-21 12:00:00',
        ]);
    }
}
