<?php

namespace Tests\Feature\Controllers;

use App\Models\Article;
use App\Models\User;
use App\Models\UserPreference;
use Illuminate\Foundation\Testing\RefreshDatabase;
use Tests\TestCase;

class UserPreferenceControllerTest extends TestCase
{
    use RefreshDatabase;

    /** @test */
    public function it_fetches_user_preferences()
    {
        $user = User::factory()->create();
        UserPreference::factory()->create([
            'user_id' => $user->id,
            'sources' => ['NewsAPI'],
            'categories' => ['Technology'],
            'authors' => ['John Doe'],
        ]);

        $token = $user->createToken('api-token')->plainTextToken;

        $response = $this->withHeader('Authorization', "Bearer $token")
            ->getJson('/api/user-preferences');

        $response->assertOk()
            ->assertJsonFragment([
                'message' => 'User preferences fetched successfully',
            ])
            ->assertJsonStructure([
                'message',
                'data' => [
                    'sources',
                    'categories',
                    'authors',
                ],
            ]);
    }

    /** @test */
    public function it_upserts_user_preferences()
    {
        $user = User::factory()->create();
        $token = $user->createToken('api-token')->plainTextToken;

        $payload = [
            'sources' => ['Guardian'],
            'categories' => ['World'],
            'authors' => ['Jane Smith'],
        ];

        $response = $this->withHeader('Authorization', "Bearer $token")
            ->postJson('/api/user-preferences', $payload);

        $response->assertOk()
            ->assertJsonFragment([
                'message' => 'User preferences saved successfully',
            ])
            ->assertJsonFragment($payload);

        $this->assertDatabaseHas('user_preferences', [
            'user_id' => $user->id,
        ]);
        $prefs = \App\Models\UserPreference::where('user_id', $user->id)->first();

        $this->assertEqualsCanonicalizing($payload['sources'], $prefs->sources);
        $this->assertEqualsCanonicalizing($payload['categories'], $prefs->categories);
        $this->assertEqualsCanonicalizing($payload['authors'], $prefs->authors);
    }

    /** @test */
    public function it_fetches_user_feed_when_preferences_exist()
    {
        $user = User::factory()->create();
        UserPreference::factory()->create([
            'user_id' => $user->id,
            'sources' => ['NewsAPI'],
            'categories' => ['Technology'],
            'authors' => ['John Doe'],
        ]);
        Article::factory()->create([
            'source' => 'NewsAPI',
            'category' => 'Technology',
            'author' => 'John Doe',
        ]);

        $token = $user->createToken('api-token')->plainTextToken;

        $response = $this->withHeader('Authorization', "Bearer $token")
            ->getJson('/api/user-feed');

        $response->assertOk()
            ->assertJsonFragment([
                'message' => 'User feed fetched successfully',
            ])
            ->assertJsonStructure([
                'message',
                'data' => [
                    'current_page',
                    'data' => [
                        ['id', 'title', 'description', 'image_url', 'source', 'author', 'category', 'published_at'],
                    ],
                    'last_page',
                    'total',
                ],
            ]);
    }

    /** @test */
    public function it_returns_error_when_fetching_feed_without_preferences()
    {
        $user = User::factory()->create();
        $token = $user->createToken('api-token')->plainTextToken;

        $response = $this->withHeader('Authorization', "Bearer $token")
            ->getJson('/api/user-feed');

        $response->assertBadRequest()
            ->assertJsonFragment([
                'message' => 'No user preferences set.',
            ]);
    }
}
