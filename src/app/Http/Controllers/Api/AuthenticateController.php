<?php

namespace App\Http\Controllers\Api;

use App\Http\Controllers\Controller;
use App\Http\Resources\UserResource;
use App\Services\UserService;
use Illuminate\Http\Request;
use Illuminate\Http\Response;
use Illuminate\Support\Facades\Auth;

class AuthenticateController extends Controller
{
    private UserService $userService;

    public function __construct(UserService $userService)
    {
        $this->userService = $userService;
    }

    /**
     * @description responsável por autenticar um usuário através da Api.
     */
    public function login(Request $request)
    {
        if (!Auth::attempt($request->only(['email', 'password']))) {
            return response()->json([
                'message' => 'Invalid login details'
            ], Response::HTTP_UNAUTHORIZED);
        }

        $user = $this->userService->authenticateUser($request);

        return response()->json(new UserResource($user), Response::HTTP_OK);
    }
}
