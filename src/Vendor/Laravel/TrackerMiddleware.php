<?php
/**
 * Created by PhpStorm.
 * User: raines
 * Date: 4/12/16
 * Time: 10:00 AM
 */

namespace PragmaRX\Tracker\Vendor\Laravel;

use Closure;
use PragmaRX\Tracker\Vendor\Laravel\Tracker;

class TrackerMiddleware
{
    /**
     * Run the request filter.
     *
     * @param \Illuminate\Http\Request $request
     * @param \Closure $next
     * @return mixed
     */
    public function handle($request, Closure $next)
    {
        Tracker::boot();
        return $next($request);
    }
}
