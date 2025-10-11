<?php

namespace App\Http\Middleware;

use Closure;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Log;
use Symfony\Component\HttpFoundation\Response;

class RequestLogger
{
    /**
     * Handle an incoming request.
     *
     * @param  \Closure(\Illuminate\Http\Request): (\Symfony\Component\HttpFoundation\Response)  $next
     */
    public function handle(Request $request, Closure $next): Response
    {
        $startTime = microtime(true);
        
        // Логируем входящий запрос
        Log::info('Incoming Request', [
            'method' => $request->method(),
            'url' => $request->fullUrl(),
            'path' => $request->path(),
            'route_name' => $request->route() ? $request->route()->getName() : 'unknown',
            'user_id' => auth()->id(),
            'ip' => $request->ip(),
            'user_agent' => $request->userAgent(),
        ]);

        try {
            $response = $next($request);
            
            $endTime = microtime(true);
            $duration = round(($endTime - $startTime) * 1000, 2);
            
            // Логируем успешный ответ
            Log::info('Response Sent', [
                'status_code' => $response->getStatusCode(),
                'duration_ms' => $duration,
                'url' => $request->fullUrl(),
            ]);
            
            return $response;
            
        } catch (\Exception $e) {
            $endTime = microtime(true);
            $duration = round(($endTime - $startTime) * 1000, 2);
            
            // Логируем ошибку
            Log::error('Request Failed', [
                'url' => $request->fullUrl(),
                'path' => $request->path(),
                'method' => $request->method(),
                'error' => $e->getMessage(),
                'file' => $e->getFile(),
                'line' => $e->getLine(),
                'trace' => $e->getTraceAsString(),
                'duration_ms' => $duration,
            ]);
            
            throw $e;
        }
    }
}