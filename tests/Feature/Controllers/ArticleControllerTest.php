<?php

namespace Tests\Feature\Controllers;

use App\Models\Article;
use Illuminate\Foundation\Testing\RefreshDatabase;
use Tests\TestCase;

class ArticleControllerTest extends TestCase
{
    use RefreshDatabase;

    /** @test */
    public function it_lists_articles_with_pagination()
    {
        Article::factory()->count(15)->create();

        $response = $this->getJson('/api/articles');

        $response->assertOk()
            ->assertJsonStructure([
                'message',
                'data' => [
                    'current_page',
                    'data' => [
                        '*' => [
                            'id',
                            'title',
                            'description',
                            'image_url',
                            'source',
                            'author',
                            'category',
                            'published_at',
                        ],
                    ],
                    'last_page',
                    'total',
                    'per_page',
                ],
            ]);
    }

    /** @test */
    public function it_applies_filters_on_articles()
    {
        Article::factory()->create([
            'title' => 'Laravel Testing Guide',
            'category' => 'tech',
            'source' => 'TestSource',
        ]);

        Article::factory()->create([
            'title' => 'Another Article',
            'category' => 'sports',
            'source' => 'OtherSource',
        ]);

        $response = $this->getJson('/api/articles?category=tech&source=TestSource');
        $response->assertOk()
            ->assertJsonFragment(['title' => 'Laravel Testing Guide'])
            ->assertJsonMissing(['title' => 'Another Article']);
    }

    /** @test */
    public function it_shows_single_article()
    {
        $article = Article::factory()->create();

        $response = $this->getJson("/api/articles/{$article->id}");

        $response->assertOk()
            ->assertJsonFragment([
                'id' => $article->id,
                'title' => $article->title,
            ]);
    }

    /** @test */
    public function it_returns_404_for_nonexistent_article()
    {
        $response = $this->getJson('/api/articles/999');

        $response->assertNotFound()
            ->assertJson([
                'message' => 'Article not found',
                'data' => [],
            ]);
    }
}
