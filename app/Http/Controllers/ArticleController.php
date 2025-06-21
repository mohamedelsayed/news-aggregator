<?php

namespace App\Http\Controllers;

use App\Models\Article;
use App\Traits\ApiResponse;
use Illuminate\Http\Request;
use Illuminate\Http\Response;

class ArticleController extends Controller
{
    use ApiResponse;

    /**
     * List articles with pagination, search, filters.
     */
    public function index(Request $request)
    {
        $query = Article::query();

        // Keyword search
        if ($request->filled('q')) {
            $query->search($request->q);
        }

        // Filter by category
        if ($request->filled('category')) {
            $query->category($request->category);
        }

        // Filter by source
        if ($request->filled('source')) {
            $query->source($request->source);
        }

        // Filter by published date (YYYY-MM-DD)
        if ($request->filled('date')) {
            $query->publishedOn($request->date);
        }

        $articles = $query
            ->summaryFields()
            ->latestPublished()
            ->paginate($request->get('per_page', Article::defaultPerPage()));

        return $this->sendResponse($articles, 'Articles fetched successfully', Response::HTTP_OK);
    }

    /**
     * Show a single article.
     */
    public function show(Request $request)
    {
        $article = Article::find($request->id);

        if (! $article) {
            return $this->sendResponse([], 'Article not found', Response::HTTP_NOT_FOUND);
        }

        return $this->sendResponse($article, 'Article fetched successfully', Response::HTTP_OK);
    }
}
