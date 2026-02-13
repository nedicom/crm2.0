<?php

namespace App\Http\Middleware;

use Closure;
use Illuminate\Http\Request;

class VerifyApiToken
{
    public function handle(Request $request, Closure $next)
    {
        // Получаем токен
        $token = $request->header('Authorization');
        
        // Если нет в заголовке, проверяем в query
        if (!$token) {
            $token = $request->query('api_token');
        }
        
        // Проверяем наличие токена
        if (!$token) {
            return response()->json([
                'success' => false,
                'error' => 'API token required',
                'message' => 'Требуется токен API'
            ], 401);
        }
        
        // Сравниваем с токеном из .env
        // ВАЖНО: в .env должен быть API_TOKEN=ваш_токен
        if ($token !== env('API_TOKEN')) {
            return response()->json([
                'success' => false,
                'error' => 'Invalid API token',
                'message' => 'Неверный токен API'
            ], 401);
        }
        
        return $next($request);
    }
}