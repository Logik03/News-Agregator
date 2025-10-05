<?php
namespace App\Repositories\Eloquent;

use App\Models\Article;
use App\Models\Author;
use App\Models\Category;
use Illuminate\Pagination\LengthAwarePaginator;
use Illuminate\Support\Str;
use App\Repositories\Contracts\ArticleRepositoryInterface;

class EloquentArticleRepository implements ArticleRepositoryInterface
{
    public function upsertMany(array $normalizedArticles, int $sourceId): int
    {
        $count = 0;
        foreach ($normalizedArticles as $a) {
            // Build selector (prefer external_id)
            $selector = ['source_id' => $sourceId];
            if (!empty($a['external_id'])) {
                $selector['external_id'] = $a['external_id'];
            } elseif (!empty($a['canonical_url'])) {
                $selector['canonical_url'] = $a['canonical_url'];
            } else {
                $selector['url'] = $a['url'] ?? null;
            }

            // Resolve author
            $authorId = null;
            if (!empty($a['author']['name'])) {
                $authorData = $a['author'];
                $author = Author::firstOrCreate(
                    ['external_id' => $authorData['external_id'] ?? null],
                    [
                        'name' => $authorData['name'] ?? 'Unknown',
                        'profile_url' => $authorData['profile_url'] ?? null,
                        'avatar_url' => $authorData['avatar_url'] ?? null,
                    ]
                );
                $authorId = $author->id;
            }

            // Resolve category (single category normalization)
            $categoryId = null;
            if (!empty($a['category'])) {
                $slug = Str::slug($a['category']);
                $category = Category::firstOrCreate(['slug' => $slug], ['name' => $a['category']]);
                $categoryId = $category->id;
            }

            $values = array_filter([
                'title' => $a['title'] ?? null,
                'excerpt' => $a['excerpt'] ?? null,
                'content' => $a['content'] ?? null,
                'image_url' => $a['image_url'] ?? null,
                'url' => $a['url'] ?? null,
                'canonical_url' => $a['canonical_url'] ?? null,
                'language' => $a['language'] ?? null,
                'published_at' => $a['published_at'] ?? null,
                'raw' => $a['raw'] ?? null,
                'author_id' => $authorId,
                'category_id' => $categoryId,
            ], fn($v) => $v !== null);

            Article::updateOrCreate($selector, $values);
            $count++;
        }

        return $count;
    }

    public function search(array $filters, int $perPage = 20): LengthAwarePaginator
    {
        $q = Article::with(['author','category','source']);

        // Keyword search
        if (!empty($filters['q'])) {
            $term = $filters['q'];
            $q->where(function($qb) use ($term) {
                $qb->where('title', 'like', "%{$term}%")
                ->orWhere('excerpt', 'like', "%{$term}%")
                ->orWhere('content', 'like', "%{$term}%");
            });
        }

        // Filter by source (id or name)
        if (!empty($filters['sources'])) {
            $sources = (array)$filters['sources'];
            $q->whereHas('source', function($query) use ($sources) {
                $query->whereIn('id', $sources)
                    ->orWhereIn('name', $sources);
            });
        }

        // Filter by category (id or name)
        if (!empty($filters['categories'])) {
            $categories = (array)$filters['categories'];
            $q->whereHas('category', function($query) use ($categories) {
                $query->whereIn('id', $categories)
                    ->orWhereIn('name', $categories)
                    ->orWhereIn('slug', $categories);
            });
        }

        // Filter by author (id or name)
        if (!empty($filters['authors'])) {
            $authors = (array)$filters['authors'];
            $q->whereHas('author', function($query) use ($authors) {
                $query->whereIn('id', $authors)
                    ->orWhereIn('name', $authors);
            });
        }

        // Date filters
        if (!empty($filters['from'])) {
            $q->whereDate('published_at', '>=', $filters['from']);
        }

        if (!empty($filters['to'])) {
            $q->whereDate('published_at', '<=', $filters['to']);
        }

        // Order by latest
        $q->orderBy('published_at', 'desc');

        return $q->paginate($perPage);
    }


    public function findById(int $id)
    {
        return Article::with(['author','category','source'])->findOrFail($id);
    }
}
