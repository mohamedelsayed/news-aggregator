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
     * @OA\Get(
     *     path="/api/articles",
     *     summary="List articles with pagination, search, and filters",
     *     tags={"Articles"},
     *
     *     @OA\Parameter(
     *         name="q",
     *         in="query",
     *         description="Search keyword in title/description/content",
     *         required=false,
     *
     *         @OA\Schema(type="string")
     *     ),
     *
     *     @OA\Parameter(
     *         name="category",
     *         in="query",
     *         description="Filter by category",
     *         required=false,
     *
     *         @OA\Schema(type="string")
     *     ),
     *
     *     @OA\Parameter(
     *         name="source",
     *         in="query",
     *         description="Filter by source",
     *         required=false,
     *
     *         @OA\Schema(type="string")
     *     ),
     *
     *     @OA\Parameter(
     *         name="date",
     *         in="query",
     *         description="Filter by published date (YYYY-MM-DD)",
     *         required=false,
     *
     *         @OA\Schema(type="string", format="date")
     *     ),
     *
     *     @OA\Parameter(
     *         name="per_page",
     *         in="query",
     *         description="Number of articles per page (default from config)",
     *         required=false,
     *
     *         @OA\Schema(type="integer", default=10)
     *     ),
     *
     *     @OA\Parameter(
     *         name="page",
     *         in="query",
     *         description="Page number",
     *         required=false,
     *
     *         @OA\Schema(type="integer", default=1)
     *     ),
     *
     *     @OA\Response(
     *         response=200,
     *         description="Articles fetched successfully",
     *
     *         @OA\JsonContent(
     *
     *             @OA\Property(property="success", type="boolean"),
     *             @OA\Property(property="message", type="string"),
     *             @OA\Property(property="data", type="object",
     *                 @OA\Property(property="current_page", type="integer"),
     *                 @OA\Property(property="data", type="array",
     *
     *                     @OA\Items(
     *
     *                         @OA\Property(property="id", type="integer"),
     *                         @OA\Property(property="title", type="string"),
     *                         @OA\Property(property="description", type="string"),
     *                         @OA\Property(property="image_url", type="string"),
     *                         @OA\Property(property="source", type="string"),
     *                         @OA\Property(property="author", type="string"),
     *                         @OA\Property(property="category", type="string"),
     *                         @OA\Property(property="published_at", type="string", format="date-time")
     *                     )
     *                 ),
     *                 @OA\Property(property="last_page", type="integer"),
     *                 @OA\Property(property="total", type="integer")
     *             )
     *         )
     *     )
     * )
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
     * @OA\Get(
     *     path="/api/articles/{id}",
     *     summary="Get a single article by ID",
     *     tags={"Articles"},
     *
     *     @OA\Parameter(
     *         name="id",
     *         in="path",
     *         description="Article ID",
     *         required=true,
     *
     *         @OA\Schema(type="integer")
     *     ),
     *
     *     @OA\Response(
     *         response=200,
     *         description="Article fetched successfully",
     *
     *         @OA\JsonContent(
     *
     *             @OA\Property(property="success", type="boolean"),
     *             @OA\Property(property="message", type="string"),
     *             @OA\Property(property="data", type="object",
     *                 @OA\Property(property="id", type="integer"),
     *                 @OA\Property(property="title", type="string"),
     *                 @OA\Property(property="description", type="string"),
     *                 @OA\Property(property="content", type="string"),
     *                 @OA\Property(property="image_url", type="string"),
     *                 @OA\Property(property="source", type="string"),
     *                 @OA\Property(property="author", type="string"),
     *                 @OA\Property(property="category", type="string"),
     *                 @OA\Property(property="published_at", type="string", format="date-time")
     *             )
     *         )
     *     ),
     *
     *     @OA\Response(
     *         response=404,
     *         description="Article not found",
     *
     *         @OA\JsonContent(
     *
     *             @OA\Property(property="success", type="boolean"),
     *             @OA\Property(property="message", type="string"),
     *             @OA\Property(property="data", type="array", @OA\Items(type="string"))
     *         )
     *     )
     * )
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
