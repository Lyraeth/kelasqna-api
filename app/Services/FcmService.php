<?php
// app/Services/FcmService.php

namespace App\Services;

use App\Models\User;
use Kreait\Firebase\Messaging\CloudMessage;
use Kreait\Firebase\Messaging\Notification;
use Kreait\Laravel\Firebase\Facades\Firebase;

class FcmService
{
    public function sendToUser(User $user, string $title, string $body, array $data = []): void
    {
        $tokens = $user->deviceTokens()->pluck('token')->toArray();

        if (empty($tokens)) return;

        $messaging = Firebase::messaging();

        $message = CloudMessage::new()
            ->withNotification(Notification::create($title, $body))
            ->withData($data);

        // Kirim ke semua device user sekaligus
        $messaging->sendMulticast($message, $tokens);
    }
}
