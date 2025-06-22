<?php

namespace App\Services;

use App\DataSource;
use App\Models\Article;
use Illuminate\Support\Facades\Http;
use Illuminate\Support\Facades\Log;

class GuardianFetcher
{
    public function fetchAndStore()
    {
        $apiKey = config('services.guardian.key');
        $url = config('services.guardian.url')."?show-fields=all&page-size=10&api-key={$apiKey}";

        $response = Http::get($url);

        if ($response->ok()) {
            foreach ($response->json('response.results') as $item) {
                try {
                    $fields = $item['fields'] ?? [];

                    Article::updateOrCreate(
                        ['url' => $item['webUrl']],
                        [
                            'title' => $item['webTitle'],
                            'description' => $fields['trailText'] ?? null,
                            'content' => $fields['bodyText'] ?? $fields['trailText'] ?? null,
                            'image_url' => $fields['thumbnail'] ?? null,
                            'source' => DataSource::GUARDIAN,
                            'author' => $fields['byline'] ?? null,
                            'category' => $item['sectionName'] ?? null,
                            'published_at' => $item['webPublicationDate'] ?? now(),
                        ]
                    );
                } catch (\Exception $e) {
                    Log::error($e);
                }
            }
        }
    }
}
