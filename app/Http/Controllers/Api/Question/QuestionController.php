<?php
// app/Http/Controllers/Api/Question/QuestionController.php

namespace App\Http\Controllers\Api\Question;

use App\Http\Controllers\Controller;
use App\Http\Resources\QuestionResource;
use App\Services\QuestionService;
use App\Models\Question;
use Illuminate\Http\JsonResponse;
use Illuminate\Http\Request;
use Illuminate\Http\Resources\Json\AnonymousResourceCollection;

class QuestionController extends Controller
{
    public function __construct(private readonly QuestionService $service) {}

    /**
     * GET /questions
     * List all questions with pagination, search & sort support.
     */
    public function index(Request $request): AnonymousResourceCollection
    {
        $filters   = $request->only(['search', 'sort', 'per_page']);
        $questions = $this->service->getAll($filters, $request->user()?->id);

        return QuestionResource::collection($questions);
    }

    /**
     * GET /questions/{id}
     * Show a single question with its comments.
     */
    public function show(int $id): QuestionResource
    {
        $question = $this->service->getById($id);

        return new QuestionResource($question);
    }

    /**
     * POST /questions
     * Create a new question (auth required).
     */
    public function store(Request $request): JsonResponse
    {
        $validated = $request->validate([
            'title'   => 'required|string|min:10|max:255',
            'content' => 'required|string|min:20',
        ]);

        $question = $this->service->create($validated);

        return (new QuestionResource($question))
            ->response()
            ->setStatusCode(201);
    }

    /**
     * PUT /questions/{question}
     * Update a question (only owner).
     */
    public function update(Request $request, Question $question): QuestionResource
    {
        $this->authorize('update', $question);

        $validated = $request->validate([
            'title'   => 'sometimes|string|min:10|max:255',
            'content' => 'sometimes|string|min:20',
        ]);

        $question = $this->service->update($question, $validated);

        return new QuestionResource($question);
    }

    /**
     * DELETE /questions/{question}
     * Delete a question (only owner).
     */
    public function destroy(Question $question): JsonResponse
    {
        $this->authorize('delete', $question);
        $this->service->delete($question);

        return response()->json([
            'message' => 'Question deleted successfully.',
        ]);
    }
}
