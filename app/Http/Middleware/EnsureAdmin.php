<?php

namespace App\Http\Middleware;

use Closure;
use Illuminate\Http\Request;
use Symfony\Component\HttpFoundation\Response;

class EnsureAdmin
{
    /**
     * Handle an incoming request.
     *
     * @param  \Closure(\Illuminate\Http\Request): (\Symfony\Component\HttpFoundation\Response)  $next
     */
    public function handle(Request $request, Closure $next): Response
    {
        /** @var \App\Models\User $user **/
        $user = auth()->user();

        // Ensure the user is authenticated and is an admin
        if (auth()->check() && $user->isAdmin()) {
            return $next($request);
        }

        // Redirect non-admin users to the dashboard with an error message
        return redirect('dashboard')->withErrors('You do not have access to this page.');
    }
}
