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
use App\Http\Middleware\HandleRefreshJwtToken;
use Illuminate\Http\Exceptions\HttpResponseException;
use Illuminate\Http\JsonResponse;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\Cookie;
use Symfony\Component\HttpFoundation\Response;

/** @todo change-password */

final class UserController extends Controller
{
    public function __construct(
        private readonly UserServiceContract $userService,
        private readonly JwtServiceContract  $jwtService,
    )
    {
    }

    public function signUp(SignUpRequest $request): JsonResponse
    {
        $user = $this->userService->signUp($request->toData());
        return $this->responseUserDataWithTokens($user);
    }

    public function signIn(SignInRequest $request): JsonResponse
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

    public function logout(): Response
    {
        return response()
            ->noContent()
            ->cookie(Cookie::forget(
                name: \Config::string('jwt.cookie.name'),
                path: '/',
                domain: \Config::string('jwt.cookie.domain'),
            ));
    }

    /**
     * @return JsonResponse
     * @throws UserNotFoundException
     */
    public function refreshToken(): JsonResponse
    {
        $userProfile = $this->userService->profile((string)Auth::id());
        return $this->responseUserDataWithTokens($userProfile);
    }

    /**
     * @throws UserNotFoundException
     */
    public function profile(): UserData
    {
        return $this->userService->profile((string)Auth::id());
    }

    private function responseUserDataWithTokens(UserData $user): JsonResponse
    {
        $refreshToken = $this->jwtService->encode(
            new JwtTokenData(
                userId: $user->id,
                type: JwtTokenType::Refresh,
                time: 3600 * 24 * 7
            )
        );

        return response()
            ->json([
                'user' => $user,
                'token' => $this->jwtService->encode(
                    new JwtTokenData(
                        userId: $user->id,
                        type: JwtTokenType::Temporary
                    )
                )
            ])
            ->cookie(Cookie::make(
                name: \Config::string('jwt.cookie.name'),
                value: $refreshToken,
                minutes: 60 * 24 * 7,
                path: '/',
                domain: \Config::string('jwt.cookie.domain'),
                secure: \Config::boolean('jwt.cookie.secure'),
                httpOnly: true,
                raw: false,
                sameSite: \Config::string('jwt.cookie.same_site'),
            ));
    }
}
