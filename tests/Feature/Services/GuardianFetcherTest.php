<?php

namespace Tests\Feature\Services;

use App\DataSource;
use App\Services\GuardianFetcher;
use Illuminate\Foundation\Testing\RefreshDatabase;
use Illuminate\Support\Facades\Http;
use Tests\TestCase;

class GuardianFetcherTest extends TestCase
{
    use RefreshDatabase;

    public function test_it_fetches_and_stores_guardian_articles()
    {
        // Mock Guardian API response
        Http::fake([
            '*' => Http::response([
                'response' => [
                    'results' => [
                        [
                            'webUrl' => 'https://www.theguardian.com/sample-article',
                            'webTitle' => 'Sample Article Title',
                            'webPublicationDate' => '2025-06-21T10:00:00Z',
                            'sectionName' => 'World',
                            'fields' => [
                                'trailText' => 'Sample trail text',
                                'bodyText' => 'Full body text of the article.',
                                'thumbnail' => 'https://media.guim.co.uk/sample-thumbnail.jpg',
                                'byline' => 'Jane Smith',
                            ],
                        ],
                    ],
                ],
            ], 200),
        ]);

        // Call the service
        $fetcher = new GuardianFetcher;
        $fetcher->fetchAndStore();

        // Assert the article exists in DB
        $this->assertDatabaseHas('articles', [
            'url' => 'https://www.theguardian.com/sample-article',
            'title' => 'Sample Article Title',
            'description' => 'Sample trail text',
            'content' => 'Full body text of the article.',
            'image_url' => 'https://media.guim.co.uk/sample-thumbnail.jpg',
            'source' => DataSource::GUARDIAN,
            'author' => 'Jane Smith',
            'category' => 'World',
            'published_at' => '2025-06-21 10:00:00',
        ]);
    }
}
