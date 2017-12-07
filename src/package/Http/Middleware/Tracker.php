<?php

namespace PragmaRX\Tracker\Package\Http\Middleware;

use Closure;

class Tracker
{
    /**
     * Handle an incoming request.
     *
     * @param \Illuminate\Http\Request $request
     * @param \Closure                 $next
     *
     * @return mixed
     */
    public function handle($request, Closure $next)
    {
        if (config('tracker.enabled')) {
            app('pragmarx.tracker')->boot();
        }

        return $next($request);
    }
}
