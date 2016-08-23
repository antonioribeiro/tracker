<?php

namespace PragmaRX\Tracker\Vendor\Laravel\Middlewares;

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
        app('tracker')->boot();

        return $next($request);
    }
}
