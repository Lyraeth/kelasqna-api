<?php

namespace App\Http\Controllers\Api\User;

use App\Http\Controllers\Controller;
use App\Http\Resources\CommentResource;
use App\Http\Resources\QuestionResource;
use App\Http\Resources\UserResource;
use App\Models\User;
use Illuminate\Http\JsonResponse;
use Illuminate\Http\Request;

class UserController extends Controller
{

    public function register(Request $request): JsonResponse
    {
        $request->validate([
            'name'         => 'required|string|max:100',
            'email'        => 'required|email|unique:users,email',
            'password'     => 'required|string|min:8|confirmed',
            'role'         => 'required|in:student,teacher',
            'class_name'   => 'required_if:role,student|nullable|string|max:50',
            'class_number' => 'required_if:role,student|nullable|string|max:10',
            'subject'      => 'required_if:role,teacher|nullable|string|max:100',
        ]);

        $user = User::create([
            'name'         => $request->name,
            'email'        => $request->email,
            'password'     => $request->password,
            'role'         => $request->role,
            'class_name'   => $request->class_name,
            'class_number' => $request->class_number,
            'subject'      => $request->subject,
        ]);

        return response()->json([
            'error'   => false,
            'message' => 'Register berhasil.',
            'user'    => new UserResource($user),
        ], 201);
    }

    public function me(Request $request): JsonResponse
    {
        return response()->json([
            'error' => false,
            'user'  => new UserResource($request->user()),
        ]);
    }

    public function show(User $user): JsonResponse
    {
        return response()->json([
            'error' => false,
            'user'  => new UserResource($user),
        ]);
    }

    public function myQuestions(Request $request): JsonResponse
    {
        $userId = $request->user()->id;

        $questions = $request->user()
            ->questions()
            ->with(['author', 'comments.author'])
            ->withCount(['comments', 'likes', 'bookmarks'])
            ->withExists([
                'likes as is_liked'          => fn($q) => $q->where('user_id', $userId),
                'bookmarks as is_bookmarked' => fn($q) => $q->where('user_id', $userId),
            ])
            ->latest()
            ->paginate(10);

        return response()->json([
            'error' => false,
            'data'  => QuestionResource::collection($questions),
            'meta'  => [
                'current_page' => $questions->currentPage(),
                'last_page'    => $questions->lastPage(),
                'total'        => $questions->total(),
            ],
        ]);
    }

    public function myComments(Request $request): JsonResponse
    {
        $comments = $request->user()
            ->comments()
            ->with(['author', 'question'])
            ->latest()
            ->paginate(10);

        return response()->json([
            'error' => false,
            'data'  => CommentResource::collection($comments),
            'meta'  => [
                'current_page' => $comments->currentPage(),
                'last_page'    => $comments->lastPage(),
                'total'        => $comments->total(),
            ],
        ]);
    }

    public function userQuestions(User $user): JsonResponse
    {
        $questions = $user->questions()
            ->with(['author', 'comments.author'])
            ->withCount('comments')
            ->latest()
            ->paginate(10);

        return response()->json([
            'error' => false,
            'data'  => QuestionResource::collection($questions),
            'meta'  => [
                'current_page' => $questions->currentPage(),
                'last_page'    => $questions->lastPage(),
                'total'        => $questions->total(),
            ],
        ]);
    }
}
