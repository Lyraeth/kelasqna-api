<?php
// app/Http/Controllers/Api/Question/LikeBookmarkController.php

namespace App\Http\Controllers\Api\Question;

use App\Http\Controllers\Controller;
use App\Http\Resources\QuestionResource;
use App\Models\Question;
use App\Services\LikeBookmarkService;
use Illuminate\Http\JsonResponse;
use Illuminate\Http\Request;

class LikeBookmarkController extends Controller
{
    public function __construct(private readonly LikeBookmarkService $service) {}

    /**
     * POST /questions/{question}/like
     * Toggle like pada question.
     */
    public function toggleLike(Request $request, Question $question): JsonResponse
    {
        $result = $this->service->toggleLike($request->user(), $question);

        return response()->json([
            'error'   => false,
            'message' => $result['liked'] ? 'Question disukai.' : 'Like dibatalkan.',
            'data'    => $result,
        ]);
    }

    /**
     * POST /questions/{question}/bookmark
     * Toggle bookmark pada question.
     */
    public function toggleBookmark(Request $request, Question $question): JsonResponse
    {
        $result = $this->service->toggleBookmark($request->user(), $question);

        return response()->json([
            'error'   => false,
            'message' => $result['bookmarked'] ? 'Question di-bookmark.' : 'Bookmark dibatalkan.',
            'data'    => $result,
        ]);
    }

    /**
     * GET /me/bookmarks
     * Lihat semua bookmark milik user yang login.
     */
    public function myBookmarks(Request $request): JsonResponse
    {
        $bookmarks = $this->service->getUserBookmarks($request->user());

        return response()->json([
            'error' => false,
            'data'  => QuestionResource::collection($bookmarks),
            'meta'  => [
                'current_page' => $bookmarks->currentPage(),
                'last_page'    => $bookmarks->lastPage(),
                'total'        => $bookmarks->total(),
            ],
        ]);
    }
}
