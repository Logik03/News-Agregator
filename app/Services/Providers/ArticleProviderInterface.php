<?php
namespace App\Services\Providers;

interface ArticleProviderInterface
{
    /**
     * Fetch articles from provider and return an array of normalized items:
     * [
     *  'external_id','title','excerpt','content','image_url','url','canonical_url',
     *  'language','published_at','author' => ['external_id','name','profile_url','avatar_url'],
     *  'category','raw'
     * ]
     */
    public function fetch(array $params = []): array;
}
