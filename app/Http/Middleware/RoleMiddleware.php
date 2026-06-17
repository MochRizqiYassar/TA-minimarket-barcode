<?php

namespace App\Http\Middleware;

use Closure;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;
use Symfony\Component\HttpFoundation\Response;

class RoleMiddleware
{
    /**
     * Handle an incoming request.
     *
     * @param  Closure(Request): (Response)  $next
     */
    public function handle($request, Closure $next, $role)
    {
        // Jika belum login, redirect ke login
        if (!Auth::check()) {
            return redirect()->route('login');
        }

        // Jika role tidak sesuai, redirect ke dashboard
        // (dashboard akan redirect ke halaman yang benar sesuai role)
        if (Auth::user()->role !== $role) {
            return redirect()->route('dashboard');
        }

        return $next($request);
    }
}
