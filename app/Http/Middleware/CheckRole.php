<?php

namespace App\Http\Middleware;

use Closure;
use Illuminate\Http\Request;

class CheckRole
{
    public function handle(Request $request, Closure $next, $role)
    {
        if (!$request->user()->hasRole($role, $request->project_id)) {
            return redirect()->route('home')->with('error', 'You do not have permission to access this resource.');
        }

        return $next($request);
    }
}
