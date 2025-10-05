<?php

namespace App\Services\Providers;

use Illuminate\Support\Facades\Http;

class NytProvider implements ArticleProviderInterface
{
    protected string $base = 'https://api.nytimes.com/svc';
    protected string $key;

    public function __construct(string $key)
    {
        $this->key = $key;
    }

    public function fetch(array $params = []): array
    {
        $defaults = [
            'api-key' => $this->key,
            'page' => 0,
        ];

        $res = Http::retry(3, 200)
            ->get($this->base . '/search/v2/articlesearch.json', array_merge($defaults, $params));

        if ($res->failed()) {
            logger()->error('NYT fetch failed', [
                'status' => $res->status(),
                'body' => $res->body(),
            ]);
            return [];
        }

        $payload = $res->json();
        $out = [];

        foreach ($payload['response']['docs'] ?? [] as $a) {
            $headline = $a['headline']['main'] ?? null;
            $byline   = $a['byline']['original'] ?? null;
            $multimedia = $a['multimedia'][0]['url'] ?? null;

            $out[] = [
                'external_id'   => $a['_id'] ?? null,
                'title'         => $headline,
                'excerpt'       => $a['abstract'] ?? null,
                'content'       => $a['lead_paragraph'] ?? null,
                'image_url'     => $multimedia ? 'https://www.nytimes.com/' . $multimedia : null,
                'url'           => $a['web_url'] ?? null,
                'canonical_url' => $a['web_url'] ?? null,
                'language'      => 'en',
                'published_at'  => $a['pub_date'] ?? null,
                'author'        => [
                    'external_id' => null,
                    'name'        => $byline,
                    'profile_url' => null,
                    'avatar_url'  => null,
                ],
                'category'      => $a['section_name'] ?? null,
                'raw'           => $a,
            ];
        }

        return $out;
    }
}
