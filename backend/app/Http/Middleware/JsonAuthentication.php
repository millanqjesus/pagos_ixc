<?php

namespace App\Http\Middleware;

use Closure;
use Illuminate\Auth\AuthenticationException;
use Illuminate\Http\Request;

class JsonAuthentication
{
    public function handle(Request $request, Closure $next)
    {
        try {
            if (!$request->bearerToken()) {
                throw new AuthenticationException('Token não fornecido');
            }

            if (!auth()->guard('sanctum')->check()) {
                throw new AuthenticationException('Token inválido');
            }

            return $next($request);
        } catch (AuthenticationException $e) {
            return response()->json([
                'status' => 'erro',
                'message' => $e->getMessage()
            ], 401);
        }
    }
}