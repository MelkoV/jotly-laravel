<?php

declare(strict_types=1);

namespace App\Http\API\v1\Controllers;

use App\Contracts\Services\JwtServiceContract;
use App\Contracts\Services\UserServiceContract;
use App\Data\User\JwtTokenData;
use App\Data\User\UserData;
use App\Enums\JwtTokenType;
use App\Http\API\v1\Requests\User\SignUpRequest;
use App\Http\Controllers\Controller;
use Illuminate\Http\Request;

final class UserController extends Controller
{
    public function __construct(
        private readonly UserServiceContract $userService,
        private readonly JwtServiceContract  $jwtService,
    ) {
    }

    public function signUp(SignUpRequest $request): \Illuminate\Http\JsonResponse
    {
        $user = $this->userService->signUp($request->toData());
        return $this->responseUserDataWithTokens($user);
    }

    public function signIn(Request $request)
    {

    }

    public function refreshToken(Request $request)
    {

    }

    public function profile()
    {

    }

    private function responseUserDataWithTokens(UserData $user): \Illuminate\Http\JsonResponse
    {
        return response()->json([
            'user' => $user,
            'token' => $this->jwtService->encode(JwtTokenData::from(['userId' => $user->id, 'type' => JwtTokenType::Temporary])),
            'refreshToken' => $this->jwtService->encode(JwtTokenData::from(['userId' => $user->id, 'type' => JwtTokenType::Permanent, 'time' => 3600 * 24])),
        ]);
    }
}
