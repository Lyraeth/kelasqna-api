<?php
// app/Services/CommentService.php

namespace App\Services;

use App\Models\Comment;
use App\Models\Question;
use App\Notifications\CommentNotification;
use Illuminate\Support\Facades\Auth;

class CommentService
{
    /**
     * Add a comment to a question.
     */
    public function create(Question $question, array $data): Comment
    {
        $comment = $question->comments()->create([
            'user_id' => Auth::id(),
            'content' => $data['content'],
        ]);

        $comment->load('author');

        if ($question->user_id !== Auth::id()) {
            if (!$question->relationLoaded('author')) {
                $question->load('author');
            }

            $questionOwner = $question->author;
            $commentOwnerName = $comment->author->name;

            app(FcmService::class)->sendToUser(
                $questionOwner,
                'Komentar Baru',
                "{$commentOwnerName} mengomentari pertanyaan kamu.",
                ['type' => 'comment', 'question_id' => (string) $question->id]
            );

            $questionOwner->notify(new CommentNotification($comment));
        }

        return $comment;
    }

    /**
     * Update a comment (only owner).
     */
    public function update(Comment $comment, array $data): Comment
    {
        $comment->update([
            'content' => $data['content'],
        ]);

        return $comment->load('author');
    }

    /**
     * Delete a comment (only owner).
     */
    public function delete(Comment $comment): void
    {
        $comment->delete();
    }
}
