<?php

declare(strict_types=1);

namespace App\Http\Middleware;

use App\Contracts\Services\JwtServiceContract;
use App\Data\User\JwtTokenData;
use App\Enums\JwtTokenType;
use App\Enums\UserStatus;
use App\Exceptions\JwtException;
use App\Models\User;
use Closure;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;
use Symfony\Component\HttpFoundation\Response;

class HandleJwtToken
{
    public function __construct(
        private readonly JwtServiceContract $jwtService
    ) {
    }

    /**
     * Handle an incoming request.
     *
     * @param  \Closure(Request): (Response)  $next
     */
    public function handle(Request $request, Closure $next): Response
    {
        try {
            $tokenData = $this->decodeToken($request->bearerToken());
            $user = User::query()->where('id', $tokenData->userId)->where('status', UserStatus::Active)->first();
            if (!$user) {
                throw new JwtException('User not found');
            }
        } catch (JwtException $e) {
            abort(401, $e->getMessage());
        }
        Auth::setUser($user);
        return $next($request);
    }

    /**
     * @param string $token
     * @return JwtTokenData
     * @throws JwtException
     */
    private function decodeToken(string $token): JwtTokenData
    {
        $data = $this->jwtService->decode($token);
        if ($data->type !== JwtTokenType::Temporary) {
            throw new JwtException('Invalid token type');
        }
        return $data;
    }
}
