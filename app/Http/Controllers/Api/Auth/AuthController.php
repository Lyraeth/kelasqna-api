<?php

namespace App\Http\Controllers\Api\Auth;

use App\Http\Controllers\Controller;
use App\Models\User;
use App\Services\SessionService;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Hash;
use Illuminate\Validation\ValidationException;

class AuthController extends Controller
{
    public function __construct(
        protected SessionService $sessionService
    ) {}

    public function login(Request $request)
    {

        $request->validate([
            'email' => 'required|email',
            'password' => 'required',
            'device_name' => 'required|string|max:255',
        ]);

        $user = User::where('email', $request->email)->first();

        if (! $user || ! Hash::check($request->password, $user->password)) {
            throw ValidationException::withMessages([
                'email' => ['The provided credentials are incorrect.'],
            ]);
        }

        $token = $this->sessionService
            ->createSessionToken($user, $request->device_name);

        return response()->json([
            'error' => false,
            'tokens' => $token
        ]);
    }

    public function logout(Request $request)
    {

        $user = $request->user();

        $this->sessionService
            ->logoutCurrentSession($user);

        return response()->json([
            'error' => false,
            'message' => 'Logged out successfully'
        ]);
    }
}
