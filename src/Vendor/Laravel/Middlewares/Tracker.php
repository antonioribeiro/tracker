<?php

namespace PragmaRX\Tracker\Vendor\Laravel\Middlewares;

use Closure;
use Config;

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
        if (Config::get('tracker.enabled')) {
            app('tracker')->boot();
        }

        return $next($request);
    }
}
