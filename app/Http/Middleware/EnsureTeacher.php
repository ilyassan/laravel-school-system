<?php

namespace App\Http\Middleware;

use Closure;
use Illuminate\Http\Request;
use Symfony\Component\HttpFoundation\Response;

class EnsureTeacher
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

        // Ensure the user is authenticated and is a teacher
        if ($user && $user->isTeacher()) {
            return $next($request);
        }

        // Redirect non-teacher users to the dashboard with an error message
        return redirect('dashboard')->withErrors('You do not have admin access.');
    }
}
