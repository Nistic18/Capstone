<?php

namespace App\Http\Middleware;

use Closure;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;

class SupplierMiddleware
{
    public function handle(Request $request, Closure $next)
    {
        // Assuming you have a 'role' field in your users table
        if (Auth::check() && Auth::user()->role === 'supplier') {
            return $next($request);
        }

        abort(403, 'Unauthorized'); // or redirect to a page
    }
}
