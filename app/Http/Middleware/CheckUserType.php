<?php

namespace App\Http\Middleware;

use Closure;
use Illuminate\Http\Request;
use Symfony\Component\HttpFoundation\Response;

class CheckUserType
{
    /**
     * Handle an incoming request.
     *
     * @param  \Closure(\Illuminate\Http\Request): (\Symfony\Component\HttpFoundation\Response)  $next
     * @param  string  $userType  The required user type (legal_professional or pro_se)
     */
    public function handle(Request $request, Closure $next, string $userType): Response
    {
        // Check if user is authenticated
        if (!auth()->check()) {
            return redirect()->route('login');
        }

        $user = auth()->user();

        // Check if user has the required user type
        if ($user->user_type !== $userType) {
            abort(403, 'Access denied. You do not have permission to access this area.');
        }

        return $next($request);
    }
}
