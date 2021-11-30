<?php

namespace App\Http\Controllers\Api;

use App\Http\Controllers\Controller;
use App\Http\Requests\UserRequest;
use App\Http\Resources\UserResource;
use App\Services\UserService;
use Illuminate\Http\Request;
use Illuminate\Http\Response;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Log;

class RegisterController extends Controller
{
    private UserService $userService;

    public function register(UserRequest $request, UserService $userService)
    {
        $this->userService = $userService;

        $input = $request->validated();

        DB::beginTransaction();

        try {
            $user = $this->userService->createUser($input['name'], $input['email'], $input['password']);

            if (!$user) {
                DB::rollBack();
                return response()->json(['message' => 'User not created'], Response::HTTP_UNPROCESSABLE_ENTITY);
            }
            DB::commit();

            return response()->json(new UserResource($user), Response::HTTP_CREATED);

        } catch (\Exception $e) {
            Log::error($e);
            DB::rollBack();

            return response()->json(['message' => 'User not created'], Response::HTTP_INTERNAL_SERVER_ERROR);
        }
    }
}
