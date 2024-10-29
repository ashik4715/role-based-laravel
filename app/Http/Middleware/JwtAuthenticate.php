<?php

namespace App\Http\Middleware;

use App\Services\Requestor\JWTException;
use App\Services\Requestor\JWTRequest;
use Closure;
use Illuminate\Http\RedirectResponse;
use Illuminate\Http\Request;
use Illuminate\Http\Response;
use PHPOpenSourceSaver\JWTAuth\Exceptions\TokenBlacklistedException;
use PHPOpenSourceSaver\JWTAuth\Exceptions\TokenExpiredException;

class JwtAuthenticate
{
    /**
     * Handle an incoming request.
     *
     * @param  Request  $request
     * @param  Closure(Request): (Response|RedirectResponse)  $next
     * @return Response|RedirectResponse
     */
    public function handle(Request $request, Closure $next)
    {
        try {
            JWTRequest::load();
        } catch (TokenExpiredException|TokenBlacklistedException $e) {
            return response(['message' => 'Token Expire'], 401);
        } catch (JWTException $e) {
            return response(['message' => $e->getMessage()], 401);
        }

        return $next($request);
    }
}
