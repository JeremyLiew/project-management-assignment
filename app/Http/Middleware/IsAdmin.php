<?php

/* @author Soo Yu Hung */

namespace App\Http\Middleware;

use Closure;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;
use Symfony\Component\HttpFoundation\Response;

class IsAdmin {

    public function handle(Request $request, Closure $next): Response {
        if (Auth::check() && Auth::user()->role === 'admin') {
            return $next($request);
        }

        return redirect()->route('dashboard')->with('error', "You don't have authorized access to this page.");
    }
}
