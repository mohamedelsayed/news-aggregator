<?php

namespace Tests\Feature\Services;

use App\DataSource;
use App\Services\NewsApiFetcher;
use Illuminate\Foundation\Testing\RefreshDatabase;
use Illuminate\Support\Facades\Http;
use Tests\TestCase;

class NewsApiFetcherTest extends TestCase
{
    use RefreshDatabase;

    public function test_it_fetches_and_stores_newsapi_articles()
    {
        // Mock NewsAPI response
        Http::fake([
            '*' => Http::response([
                'articles' => [
                    [
                        'url' => 'https://newsapi.org/sample-article',
                        'title' => 'Sample NewsAPI Title',
                        'description' => 'Sample description from NewsAPI.',
                        'content' => 'Full content from NewsAPI.',
                        'urlToImage' => 'https://newsapi.org/sample-image.jpg',
                        'author' => 'John Doe',
                        'publishedAt' => '2025-06-21T11:00:00Z',
                    ],
                ],
            ], 200),
        ]);

        // Call the service
        $fetcher = new NewsApiFetcher;
        $fetcher->fetchAndStore();

        // Assert the article exists in DB
        $this->assertDatabaseHas('articles', [
            'url' => 'https://newsapi.org/sample-article',
            'title' => 'Sample NewsAPI Title',
            'description' => 'Sample description from NewsAPI.',
            'content' => 'Full content from NewsAPI.',
            'image_url' => 'https://newsapi.org/sample-image.jpg',
            'source' => DataSource::NEWS_API,
            'author' => 'John Doe',
            'category' => null,
            'published_at' => '2025-06-21 11:00:00',
        ]);
    }
}
