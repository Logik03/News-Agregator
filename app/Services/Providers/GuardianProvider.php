<?php

namespace App\Services\Providers;

use Illuminate\Support\Facades\Http;

class GuardianProvider implements ArticleProviderInterface
{
    protected string $base = 'https://content.guardianapis.com';
    protected string $key;

    public function __construct(string $key)
    {
        $this->key = $key;
    }

    public function fetch(array $params = []): array
    {
        $defaults = [
            'page-size' => 50,
            'show-fields' => 'headline,trailText,body,thumbnail,byline',
            'api-key' => $this->key,
        ];

        $res = Http::retry(3, 200)
            ->get($this->base . '/search', array_merge($defaults, $params));

        if ($res->failed()) {
            logger()->error('Guardian fetch failed', [
                'status' => $res->status(),
                'body' => $res->body(),
            ]);
            return [];
        }

        $payload = $res->json();
        $out = [];

        foreach ($payload['response']['results'] ?? [] as $a) {
            $fields = $a['fields'] ?? [];
            $out[] = [
                'external_id'   => $a['id'] ?? null,
                'title'         => $fields['headline'] ?? null,
                'excerpt'       => $fields['trailText'] ?? null,
                'content'       => $fields['body'] ?? null,
                'image_url'     => $fields['thumbnail'] ?? null,
                'url'           => $a['webUrl'] ?? null,
                'canonical_url' => $a['webUrl'] ?? null,
                'language'      => 'en',
                'published_at'  => $a['webPublicationDate'] ?? null,
                'author'        => [
                    'external_id' => null,
                    'name'        => $fields['byline'] ?? null,
                    'profile_url' => null,
                    'avatar_url'  => null,
                ],
                'category'      => $a['sectionName'] ?? null,
                'raw'           => $a,
            ];
        }

        return $out;
    }
}
