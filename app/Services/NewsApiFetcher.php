<?php

namespace App\Services;

use App\DataSource;
use App\Models\Article;
use Illuminate\Support\Facades\Http;

class NewsApiFetcher
{
    public function fetchAndStore()
    {
        $apiKey = config('services.newsApi.key');
        $url = config('services.newsApi.url')."?language=en&pageSize=10&apiKey={$apiKey}";

        $response = Http::get($url);

        if ($response->ok()) {
            foreach ($response->json('articles') as $item) {
                Article::updateOrCreate(
                    ['url' => $item['url']],
                    [
                        'title' => $item['title'],
                        'description' => $item['description'],
                        'content' => $item['content'] ?? $item['description'],
                        'image_url' => $item['urlToImage'],
                        'source' => DataSource::NEWS_API,
                        'author' => $item['author'],
                        'category' => null,
                        'published_at' => $item['publishedAt'],
                    ]
                );
            }
        }
    }
}
