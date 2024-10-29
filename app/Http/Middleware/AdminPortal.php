<?php

namespace App\Http\Middleware;

use Closure;
use Illuminate\Http\RedirectResponse;
use Illuminate\Http\Request;
use Illuminate\Http\Response;

class AdminPortal
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
        if (is_null($request->header('x-api-key')) || config('app.api_key') !== $request->header('x-api-key')) {
            return response(['message' => 'Unauthorized'], 401);
        }

        return $next($request);
    }
}
