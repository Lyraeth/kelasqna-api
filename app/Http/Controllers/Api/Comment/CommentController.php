<?php
// app/Http/Controllers/Api/Comment/CommentController.php

namespace App\Http\Controllers\Api\Comment;

use App\Http\Controllers\Controller;
use App\Http\Resources\CommentResource;
use App\Models\Comment;
use App\Models\Question;
use App\Services\CommentService;
use Illuminate\Http\JsonResponse;
use Illuminate\Http\Request;

class CommentController extends Controller
{
    public function __construct(private readonly CommentService $service) {}

    /**
     * POST /questions/{question}/comments
     * Add a comment to a question.
     */
    public function store(Request $request, Question $question): JsonResponse
    {
        $validated = $request->validate([
            'content' => 'required|string|min:2|max:1000',
        ]);

        $comment = $this->service->create($question, $validated);

        return (new CommentResource($comment))
            ->response()
            ->setStatusCode(201);
    }

    /**
     * PUT /questions/{question}/comments/{comment}
     * Update a comment (only owner).
     */
    public function update(Request $request, Question $question, Comment $comment): CommentResource
    {
        $this->authorize('update', $comment);

        $validated = $request->validate([
            'content' => 'required|string|min:2|max:1000',
        ]);

        $comment = $this->service->update($comment, $validated);

        return new CommentResource($comment);
    }

    /**
     * DELETE /questions/{question}/comments/{comment}
     * Delete a comment (only owner).
     */
    public function destroy(Question $question, Comment $comment): JsonResponse
    {
        $this->authorize('delete', $comment);
        $this->service->delete($comment);

        return response()->json([
            'message' => 'Comment deleted successfully.',
        ]);
    }
}
