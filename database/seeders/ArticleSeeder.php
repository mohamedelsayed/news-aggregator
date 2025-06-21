<?php

namespace Database\Seeders;

use App\Models\Article;
use Carbon\Carbon;
use Illuminate\Database\Seeder;

class ArticleSeeder extends Seeder
{
    /**
     * Run the database seeds.
     */
    public function run(): void
    {
        $sources = ['NewsAPI', 'Guardian', 'NYTimes'];
        $categories = ['Technology', 'Health', 'Environment', 'Politics'];

        foreach (range(1, 20) as $i) {
            Article::create([
                'title' => "Sample Article Title $i",
                'description' => "This is a short description for article $i.",
                'content' => "This is the full content for article $i. It includes more detailed information.",
                'url' => "https://example.com/article-$i",
                'image_url' => "https://via.placeholder.com/600x400.png?text=Article+$i",
                'source' => $sources[array_rand($sources)],
                'author' => "Author $i",
                'category' => $categories[array_rand($categories)],
                'published_at' => Carbon::now()->subDays(rand(0, 10)),
            ]);
        }
    }
}
