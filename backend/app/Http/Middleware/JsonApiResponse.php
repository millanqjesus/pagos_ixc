<?php

namespace App\Http\Middleware;

use Closure;
use Illuminate\Http\Request;
use Illuminate\Auth\AuthenticationException;
use Symfony\Component\HttpKernel\Exception\UnauthorizedHttpException;

class JsonApiResponse
{
    public function handle(Request $request, Closure $next)
    {
        try {
            $response = $next($request);

            // Si la respuesta ya es JSON, la dejamos pasar
            if ($response->headers->get('Content-Type') === 'application/json') {
                return $response;
            }

            // Convertimos la respuesta a nuestro formato JSON estándar
            if ($response->getStatusCode() === 200) {
                $original = $response->getOriginalContent();
                return response()->json([
                    'status' => 'sucesso',
                    'data' => $original
                ]);
            }

            return $response;

        } catch (AuthenticationException $e) {
            return response()->json([
                'status' => 'erro',
                'message' => 'Token inválido ou não fornecido'
            ], 401);
        } catch (UnauthorizedHttpException $e) {
            return response()->json([
                'status' => 'erro',
                'message' => 'Não autorizado'
            ], 401);
        }
    }
}