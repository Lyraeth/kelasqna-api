<?php
// app/Services/LikeBookmarkService.php

namespace App\Services;

use App\Models\Question;
use App\Models\User;
use App\Notifications\BookmarkNotification;
use App\Notifications\LikeNotification;

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

        // ✅ !$isLiked = baru di-like, bukan unlike
        if (!$isLiked && $question->user_id !== $user->id) {
            $question->loadMissing('author');

            app(FcmService::class)->sendToUser(
                $question->author,  // ✅ bukan ->user
                'Pertanyaan Kamu Disukai',
                "{$user->name} menyukai pertanyaan kamu.",
                ['type' => 'like', 'question_id' => (string) $question->id]
            );

            $question->author->notify(new LikeNotification($user, $question));
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

        // ✅ !$isBookmarked = baru di-bookmark, bukan un-bookmark
        if (!$isBookmarked && $question->user_id !== $user->id) {
            $question->loadMissing('author');

            app(FcmService::class)->sendToUser(
                $question->author,  // ✅ bukan ->user
                'Pertanyaan Kamu Di-bookmark',
                "{$user->name} mem-bookmark pertanyaan kamu.",
                ['type' => 'bookmark', 'question_id' => (string) $question->id]
            );

            $question->author->notify(new BookmarkNotification($user, $question));
        }

        return [
            'bookmarked'      => !$isBookmarked,
            'bookmarks_count' => $question->bookmarks()->count(),
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
