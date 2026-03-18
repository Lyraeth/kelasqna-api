<?php

namespace App\Http\Controllers\Api\Notification;

use App\Http\Controllers\Controller;
use App\Models\DeviceToken;
use Illuminate\Http\Request;
use Symfony\Component\HttpFoundation\JsonResponse;

class DeviceTokenController extends Controller
{
    public function store(Request $request): JsonResponse
    {
        $request->validate([
            'token'       => 'required|string',
            'device_type' => 'nullable|in:android,ios',
        ]);

        // Upsert: kalau token udah ada, skip. Kalau belum, insert.
        DeviceToken::firstOrCreate(
            ['token' => $request->token],
            [
                'user_id'     => $request->user()->id,
                'device_type' => $request->device_type,
            ]
        );

        return response()->json(['message' => 'Device token saved.']);
    }

    public function destroy(Request $request): JsonResponse
    {
        $request->validate(['token' => 'required|string']);

        DeviceToken::where('token', $request->token)
            ->where('user_id', $request->user()->id)
            ->delete();

        return response()->json(['message' => 'Device token removed.']);
    }
}
