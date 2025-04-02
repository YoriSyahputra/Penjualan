<?php

namespace App\Http\Middleware;

use Closure;
use Illuminate\Http\Request;

class DriverMiddleware
{
        public function handle($request, Closure $next)
    {
        if (!auth()->user()->is_driver) {
            return redirect('/driver/dashboard')->with('error', 'Unauthorized access');
        }
        return $next($request);
    }
}
