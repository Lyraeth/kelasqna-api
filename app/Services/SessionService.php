<?php

namespace App\Services;

use App\Models\User;
use Illuminate\Support\Carbon;


class SessionService
{

    public function createSessionToken(User $user, string $deviceName): array
    {
        $expiresAt = Carbon::now()->addDays(1);

        $accessToken = $user->createToken(
            $deviceName,
            ['question:write', 'comment:write'],
            $expiresAt
        )->plainTextToken;

        return [
            'access_token' => $accessToken,
            'token_type'   => 'Bearer',
            'expires_at'   => $expiresAt,
        ];
    }

    public function listSessions(User $user)
    {
        return $user->tokens()
            ->get()
            ->map(function ($token) {
                return [
                    'id' => $token->id,
                    'device_name' => $token->name,
                    'last_used_at' => $token->last_used_at,
                    'expires_at' => $token->expires_at,
                ];
            });
    }

    public function revokeSession(User $user, int $tokenId): void
    {
        $user->tokens()
            ->where('id', $tokenId)
            ->delete();
    }

    public function logoutCurrentSession(User $user): void
    {
        $user->tokens()
            ->where('id', $user->currentAccessToken()?->id)
            ->delete();
    }
}
