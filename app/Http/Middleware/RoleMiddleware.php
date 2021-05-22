<?php

namespace App\Http\Middleware;

use Closure;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;

class RoleMiddleware
{
    /**
     * Handle an incoming request.
     *
     * @param  \Illuminate\Http\Request  $request
     * @param  \Closure  $next
     * @return mixed
     */
    public function handle(Request $request, Closure $next)
    {
        $roles = $request->route()->getAction('roles');

        if ($roles && ! in_array(Auth::user()->role, $roles)) {
            if ($request->expectsJson()) {
                return response()->json(['Forbidden'], 403);
            }

            return redirect('/');
        }

        return $next($request);
    }
}
