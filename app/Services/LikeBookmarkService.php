<?php
// app/Services/LikeBookmarkService.php

namespace App\Services;

use App\Models\Question;
use App\Models\User;

class LikeBookmarkService
{
    /**
     * Toggle like — kalau udah like, unlike. Kalau belum, like.
     */
    public function toggleLike(User $user, Question $question): array
    {
        $isLiked = $question->likes()->where('user_id', $user->id)->exists();

        if ($isLiked) {
            $question->likes()->detach($user->id);
        } else {
            $question->likes()->attach($user->id);
        }

        return [
            'liked'       => !$isLiked,
            'likes_count' => $question->likes()->count(),
        ];
    }

    /**
     * Toggle bookmark — sama seperti like.
     */
    public function toggleBookmark(User $user, Question $question): array
    {
        $isBookmarked = $question->bookmarks()->where('user_id', $user->id)->exists();

        if ($isBookmarked) {
            $question->bookmarks()->detach($user->id);
        } else {
            $question->bookmarks()->attach($user->id);
        }

        return [
            'bookmarked'       => !$isBookmarked,
            'bookmarks_count'  => $question->bookmarks()->count(),
        ];
    }

    /**
     * Get semua bookmarks milik user.
     */
    public function getUserBookmarks(User $user)
    {
        return $user->bookmarkedQuestions()
            ->with(['author', 'comments.author'])
            ->withCount(['likes', 'bookmarks', 'comments'])
            ->withExists([
                'likes as is_liked'          => fn($q) => $q->where('user_id', $user->id),
                'bookmarks as is_bookmarked' => fn($q) => $q->where('user_id', $user->id),
            ])
            ->latest('question_bookmarks.created_at')
            ->paginate(10);
    }
}
