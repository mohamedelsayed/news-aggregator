<?php

namespace App\Http\Controllers;

use App\Http\Requests\UserPreferenceRequest;
use App\Models\Article;
use App\Models\UserPreference;
use App\Traits\ApiResponse;
use Illuminate\Http\Request;
use Illuminate\Http\Response;

class UserPreferenceController extends Controller
{
    use ApiResponse;

    public function show(Request $request)
    {
        $prefs = $request->user()->preference ?? new UserPreference;

        return $this->sendResponse($prefs, 'User preferences fetched successfully', Response::HTTP_OK);
    }

    public function upsert(UserPreferenceRequest $request)
    {
        $prefs = UserPreference::updateOrCreate(
            ['user_id' => $request->user()->id],
            $request->only(['sources', 'categories', 'authors'])
        );

        return $this->sendResponse($prefs, 'User preferences saved successfully', Response::HTTP_OK);
    }

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
