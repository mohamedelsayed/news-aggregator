<?php

namespace App\Services;

use App\DataSource;
use App\Models\Article;
use Illuminate\Support\Facades\Http;
use Illuminate\Support\Facades\Log;

class NewYorkTimesFetcher
{
    public function fetchAndStore()
    {
        $apiKey = config('services.newYorkTimes.key');
        $url = config('services.newYorkTimes.url')."?api-key={$apiKey}";

        $response = Http::get($url);

        if ($response->ok()) {
            foreach ($response->json('results') as $item) {
                try {
                    // Get first multimedia image if exists
                    $image = null;
                    if (! empty($item['multimedia'])) {
                        $image = $item['multimedia'][0]['url'] ?? null;
                    }

                    Article::updateOrCreate(
                        ['url' => $item['url']],
                        [
                            'title' => $item['title'],
                            'description' => $item['abstract'],
                            'content' => $item['abstract'],
                            'image_url' => $image,
                            'source' => DataSource::NEW_YORK_TIMES,
                            'author' => $item['byline'] ?? null,
                            'category' => $item['section'] ?? null,
                            'published_at' => $item['published_date'] ?? now(),
                        ]
                    );
                } catch (\Exception $e) {
                    Log::error($e);
                }
            }
        }
    }
}
