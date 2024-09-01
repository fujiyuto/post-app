<?php

namespace App\Http\Middleware;

use Closure;
use Illuminate\Http\Request;
use Symfony\Component\HttpFoundation\Response;
use Illuminate\Support\Facades\Log;

class LogRoute
{
    /**
     * Handle an incoming request.
     *
     * @param  \Closure(\Illuminate\Http\Request): (\Symfony\Component\HttpFoundation\Response)  $next
     */
    public function handle(Request $request, Closure $next): Response
    {
        $route = $request->route();
        $actionName = $route ? $route->getActionName() : 'Undefined route';
        $routeName = $route ? $route->getName() : 'Undefined route name';
        Log::info('Route: ' . $routeName . ' Action: ' . $actionName);

        return $next($request);
    }
}
