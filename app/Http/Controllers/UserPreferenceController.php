<?php

namespace App\Http\Controllers;

use App\Http\Requests\UserPreferenceRequest;
use App\Models\Article;
use App\Models\UserPreference;
use Illuminate\Http\Request;
use Illuminate\Http\Response;

class UserPreferenceController extends Controller
{
    /**
     * @OA\Get(
     *     path="/api/user-preferences",
     *     summary="Get current user's preferences",
     *     tags={"User Preferences"},
     *     security={{"sanctum": {}}},
     *
     *     @OA\Response(
     *         response=200,
     *         description="User preferences fetched successfully",
     *
     *         @OA\JsonContent(
     *
     *             @OA\Property(property="message", type="string"),
     *             @OA\Property(property="data", type="object",
     *                 @OA\Property(property="sources", type="array", @OA\Items(type="string")),
     *                 @OA\Property(property="categories", type="array", @OA\Items(type="string")),
     *                 @OA\Property(property="authors", type="array", @OA\Items(type="string"))
     *             )
     *         )
     *     )
     * )
     */
    public function show(Request $request)
    {
        $prefs = $request->user()->preference ?? new UserPreference;

        return $this->sendResponse($prefs, 'User preferences fetched successfully', Response::HTTP_OK);
    }

    /**
     * @OA\Post(
     *     path="/api/user-preferences",
     *     summary="Set or update user preferences",
     *     tags={"User Preferences"},
     *     security={{"sanctum": {}}},
     *
     *     @OA\RequestBody(
     *         required=true,
     *
     *         @OA\JsonContent(
     *
     *             @OA\Property(property="sources", type="array", @OA\Items(type="string"), example={"NewsAPI","Guardian"}),
     *             @OA\Property(property="categories", type="array", @OA\Items(type="string"), example={"Technology","World"}),
     *             @OA\Property(property="authors", type="array", @OA\Items(type="string"), example={"John Doe","Jane Smith"})
     *         )
     *     ),
     *
     *     @OA\Response(
     *         response=200,
     *         description="User preferences saved successfully",
     *
     *         @OA\JsonContent(
     *
     *             @OA\Property(property="message", type="string"),
     *             @OA\Property(property="data", type="object",
     *                 @OA\Property(property="sources", type="array", @OA\Items(type="string")),
     *                 @OA\Property(property="categories", type="array", @OA\Items(type="string")),
     *                 @OA\Property(property="authors", type="array", @OA\Items(type="string"))
     *             )
     *         )
     *     )
     * )
     */
    public function upsert(UserPreferenceRequest $request)
    {
        $prefs = UserPreference::updateOrCreate(
            ['user_id' => $request->user()->id],
            $request->only(['sources', 'categories', 'authors'])
        );

        return $this->sendResponse($prefs, 'User preferences saved successfully', Response::HTTP_OK);
    }

    /**
     * @OA\Get(
     *     path="/api/user-feed",
     *     summary="Get personalized news feed based on user preferences",
     *     tags={"User Preferences"},
     *     security={{"sanctum": {}}},
     *
     *     @OA\Parameter(
     *         name="per_page",
     *         in="query",
     *         description="Number of articles per page",
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
     *         description="User feed fetched successfully",
     *
     *         @OA\JsonContent(
     *
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
     *     ),
     *
     *     @OA\Response(
     *         response=400,
     *         description="No user preferences set",
     *
     *         @OA\JsonContent(
     *
     *             @OA\Property(property="message", type="string"),
     *             @OA\Property(property="data", type="array", @OA\Items(type="string"))
     *         )
     *     )
     * )
     */
    public function feed(Request $request)
    {
        $prefs = $request->user()->preference;

        if (! $prefs) {
            return $this->sendResponse([], 'No user preferences set.', Response::HTTP_BAD_REQUEST);
        }

        $articles = Article::query()
            ->summaryFields()
            ->latestPublished()
            ->matchPreferences($prefs)
            ->paginate($request->get('per_page', Article::defaultPerPage()));

        return $this->sendResponse($articles, 'User feed fetched successfully', Response::HTTP_OK);
    }
}
