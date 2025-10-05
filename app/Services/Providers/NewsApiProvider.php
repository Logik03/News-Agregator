<?php

namespace App\Services\Providers;

use Illuminate\Support\Facades\Http;

class NewsApiProvider implements ArticleProviderInterface
{
    protected string $base = 'https://newsapi.org/v2';
    protected string $key;

    /**
     * Constructor receives API key via dependency injection.
     */
    public function __construct(string $key)
    {
        $this->key = $key;
    }

    /**
     * Fetch articles from NewsAPI.
     *
     * @param array $params
     * @return array
     */
    public function fetch(array $params = []): array
    {
        $defaults = [
            'q' => 'news',
            'pageSize' => 50,
            'language' => 'en',
        ];

        // Merge defaults and params, and always include the API key
        $query = array_merge($defaults, $params, ['apiKey' => $this->key]);

        // Make request with retries
        $response = Http::retry(3, 200)
            ->get("{$this->base}/everything", $query);

        if ($response->failed()) {
            // Log failure for debugging
            logger()->error('NewsAPI fetch failed', [
                'status' => $response->status(),
                'body' => $response->body(),
            ]);
            return [];
        }

        $payload = $response->json();
        $articles = [];

        foreach ($payload['articles'] ?? [] as $a) {
            $articles[] = [
                'external_id' => $a['url'] ?? null,
                'title' => $a['title'] ?? null,
                'excerpt' => $a['description'] ?? null,
                'content' => $a['content'] ?? null,
                'image_url' => $a['urlToImage'] ?? null,
                'url' => $a['url'] ?? null,
                'canonical_url' => $a['url'] ?? null,
                'language' => $a['language'] ?? 'en',
                'published_at' => $a['publishedAt'] ?? null,
                'author' => [
                    'external_id' => null,
                    'name' => $a['author'] ?? null,
                    'profile_url' => null,
                    'avatar_url' => null
                ],
                'category' => $params['category'] ?? null,
                'raw' => $a,
            ];
        }

        return $articles;
    }
}
