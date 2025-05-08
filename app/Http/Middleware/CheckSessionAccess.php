<?php

namespace App\Http\Middleware;

use Closure;
use Illuminate\Http\Request;
use Symfony\Component\HttpFoundation\Response;

class CheckSessionAccess
{
    /**
     * Handle an incoming request.
     *
     * @param  \Closure(\Illuminate\Http\Request): (\Symfony\Component\HttpFoundation\Response)  $next
     */
    public function handle(Request $request, Closure $next): Response
    {
        // Check if session has the necessary data
        if (session()->has('token') && session()->has('address') && session()->has('port')) {
            return $next($request); // Allow access if session data is available
        }

        // Redirect or deny access if session data is missing
        return redirect()->route('login')->withErrors(['global' => 'You need to log in first.']);
    }
}
