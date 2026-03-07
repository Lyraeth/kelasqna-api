<?php

namespace App\Http\Controllers\Api\Session;

use App\Http\Controllers\Controller;
use App\Services\SessionService;
use Illuminate\Http\Request;

class SessionController extends Controller
{

    public function __construct(
        protected SessionService $sessionService
    ) {}

    public function devices(Request $request)
    {
        return response()->json([
            'devices' => $this->sessionService
                ->listSessions($request->user())
        ]);
    }

    public function revokeSession(Request $request)
    {

        $request->validate([
            'token_id' => 'required|integer|exists:personal_access_tokens,id'
        ]);

        $this->sessionService
            ->revokeSession($request->user(), $request->token_id);


        return response()->json([
            'error' => false,
            'message' => 'Session successfully deleted'
        ]);
    }
}
