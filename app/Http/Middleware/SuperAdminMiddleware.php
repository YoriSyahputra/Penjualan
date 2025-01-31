<?php

namespace App\Http\Middleware;

use Closure;
use Illuminate\Http\Request;

class SuperAdminMiddleware
{
    public function handle(Request $request, Closure $next)
    {
        if (!auth()->user()->is_super_admin) {
            return redirect('/')->with('error', 'Unauthorized access');
        }

        return $next($request);
    }
}

