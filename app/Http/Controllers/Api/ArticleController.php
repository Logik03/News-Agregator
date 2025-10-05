<?php

namespace App\Http\Controllers\Api;

use App\Http\Controllers\Controller;
use App\Http\Requests\ArticleFilterRequest;
use App\Repositories\Contracts\ArticleRepositoryInterface;
use Illuminate\Http\Request;

class ArticleController extends Controller
{
    protected ArticleRepositoryInterface $repo;

    public function __construct(ArticleRepositoryInterface $repo) {
        $this->repo = $repo;
    }

    // GET /api/articles
    public function index(ArticleFilterRequest $request) {
        $filters = $request->validated();
        $perPage = $filters['per_page'] ?? 20;
        $paginator = $this->repo->search($filters, $perPage);
        return response()->json($paginator);
    }

    // GET /api/articles/{article}
    public function show($id)
    {
        $article = $this->repo->findById((int) $id);
        return response()->json($article);
    }
}
