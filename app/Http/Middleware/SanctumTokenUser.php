<?php

namespace App\Http\Middleware;
use Closure;
use Illuminate\Http\Request;
use Laravel\Sanctum\PersonalAccessToken;

class SanctumTokenUser
{
    public function handle(Request $request, Closure $next)
    {
        $header = $request->header('Authorization');
        $token = null;
        if ($header && str_starts_with($header, 'Bearer ')) {
            $token = substr($header, 7);
        }

        if ($token) {
            $accessToken = PersonalAccessToken::findToken($token);
            if ($accessToken) {
                $request->setUserResolver(fn() => $accessToken->tokenable);
            }
        }

        if (!$request->user()) {
            return response()->json(['status' => false, 'message' => 'Invalid token'], 401);
        }

        return $next($request);
    }
}
