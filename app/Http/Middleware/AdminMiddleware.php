<?php

namespace App\Http\Middleware;

use Closure;
use Illuminate\Http\Request;

class AdminMiddleware
{
        public function handle($request, Closure $next)
    {
        if (!auth()->user()->is_admin) {
            return redirect('/')->with('error', 'Unauthorized access');
        }
        return $next($request);
    }
}
