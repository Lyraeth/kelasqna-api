<?php

namespace App\Services;

use App\Models\Question;
use Illuminate\Contracts\Pagination\LengthAwarePaginator;
use Illuminate\Support\Facades\Auth;

class QuestionService
{
    /**
     * Get paginated list of questions with author & comment count.
     */
    public function getAll(array $filters = [], ?int $userId = null): LengthAwarePaginator
    {
        return Question::query()
            ->with(['author', 'comments.author'])->withCount(['comments', 'likes', 'bookmarks'])
            ->when($userId, fn($q) => $q->withExists([
                'likes as is_liked'           => fn($q) => $q->where('user_id', $userId),
                'bookmarks as is_bookmarked'  => fn($q) => $q->where('user_id', $userId),
            ]))
            ->when(
                isset($filters['search']),
                fn($q) =>
                $q->where('title', 'like', '%' . $filters['search'] . '%')
                    ->orWhere('content', 'like', '%' . $filters['search'] . '%')
            )
            ->when(isset($filters['sort']), function ($q) use ($filters) {
                match ($filters['sort']) {
                    'popular'  => $q->orderByDesc('likes_count'),
                    'comments' => $q->orderByDesc('comments_count'),
                    default    => $q->latest(),
                };
            }, fn($q) => $q->latest())
            ->paginate($filters['per_page'] ?? 10);
    }

    /**
     * Get single question with all comments.
     */
    public function getById(int $id): Question
    {
        return Question::with(['author', 'comments.author'])->withCount(['comments', 'likes', 'bookmarks'])
            ->findOrFail($id);
    }

    /**
     * Create a new question.
     */
    public function create(array $data): Question
    {
        $question = Question::create([
            'user_id' => Auth::id(),
            'title'   => $data['title'],
            'content' => $data['content'],
        ]);

        return $question->load(['author', 'comments.author']);
    }

    /**
     * Update an existing question.
     */
    public function update(Question $question, array $data): Question
    {
        $question->update([
            'title'   => $data['title']   ?? $question->title,
            'content' => $data['content'] ?? $question->content,
        ]);

        return $question->load(['author', 'comments.author']);
    }

    /**
     * Delete a question (soft delete).
     */
    public function delete(Question $question): void
    {
        $question->delete();
    }
}
