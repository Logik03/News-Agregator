<?php
namespace App\Repositories\Contracts;

use Illuminate\Pagination\LengthAwarePaginator;

interface ArticleRepositoryInterface
{
   
    public function upsertMany(array $normalizedArticles, int $sourceId): int;
    public function search(array $filters, int $perPage = 20): LengthAwarePaginator;
    public function findById(int $id);
}
