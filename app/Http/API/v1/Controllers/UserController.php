<?php

declare(strict_types=1);

namespace App\Http\API\v1\Controllers;

use App\Contracts\Services\JwtServiceContract;
use App\Contracts\Services\UserServiceContract;
use App\Data\User\JwtTokenData;
use App\Data\User\UserData;
use App\Enums\JwtTokenType;
use App\Exceptions\UserNotFoundException;
use App\Http\API\v1\Requests\User\SignInRequest;
use App\Http\API\v1\Requests\User\SignUpRequest;
use App\Http\Controllers\Controller;
use Illuminate\Http\Exceptions\HttpResponseException;
use Symfony\Component\HttpFoundation\Response;

final class UserController extends Controller
{
    public function __construct(
        private readonly UserServiceContract $userService,
        private readonly JwtServiceContract $jwtService,
    ) {
    }

    public function signUp(SignUpRequest $request): \Illuminate\Http\JsonResponse
    {
        $user = $this->userService->signUp($request->toData());
        return $this->responseUserDataWithTokens($user);
    }

    public function signIn(SignInRequest $request): \Illuminate\Http\JsonResponse
    {
        try {
            $user = $this->userService->signIn($request->toData());
        } catch (UserNotFoundException) {
            throw new HttpResponseException(response()->json([
                'errors' => ['email' => [__('auth.failed')]],
                'message' => __('auth.failed'),
            ], Response::HTTP_UNPROCESSABLE_ENTITY));
        }
        return $this->responseUserDataWithTokens($user);
    }

    /*public function refreshToken(Request $request)
    {

    }

    public function profile()
    {

    }*/

    private function responseUserDataWithTokens(UserData $user): \Illuminate\Http\JsonResponse
    {
        return response()->json([
            'user' => $user,
            'token' => $this->jwtService->encode(
                new JwtTokenData(
                    userId: $user->id,
                    type:
                    JwtTokenType::Temporary
                )
            ),
            'refreshToken' => $this->jwtService->encode(
                new JwtTokenData(
                    userId: $user->id,
                    type: JwtTokenType::Permanent,
                    time: 3600 * 24
                )
            ),
        ]);
    }
}
