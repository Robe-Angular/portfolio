<?php

namespace App\Http\Middleware;

use Closure;
use Illuminate\Http\Request;
use Symfony\Component\HttpFoundation\Response;
use PHPOpenSourceSaver\JWTAuth\Facades\JWTAuth;
use PHPOpenSourceSaver\JWTAuth\Http\Middleware\BaseMiddleware;
use  Exception;

class BearerTokenMiddleware extends BaseMiddleware
{
    /**
     * Handle an incoming request.
     *
     * @param  \Closure(\Illuminate\Http\Request): (\Symfony\Component\HttpFoundation\Response)  $next
     */
    public function handle(Request $request, Closure $next, $guard = null): Response
    {
        if($guard != null)
             try {
                if($guard=='consumer'){
                    $user=auth('consumer')->user();
                }
                if($guard == 'admin'){
                    $user=auth('admin')->user();
                }
                    
                JWTAuth::parseToken()->authenticate();
                auth()->shouldUse($guard);
                if (!$user) {
                    throw new Exception('Invalid JWT subject');
                }
            } catch (\Throwable $e) {
                return response()->json(['error' => 'Unauthorized'], 401);
            }
            
        return $next($request);
    }
}
