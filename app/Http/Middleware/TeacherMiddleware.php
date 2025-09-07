<?php

namespace App\Http\Middleware;

use Closure;
use Illuminate\Http\Request;
use Symfony\Component\HttpFoundation\Response;

class TeacherMiddleware
{
    /**
     * Handle an incoming request.
     *
     * @param  \Closure(\Illuminate\Http\Request): (\Symfony\Component\HttpFoundation\Response)  $next
     */
    public function handle(Request $request, Closure $next): Response
    {
        $user = auth()->user();
        
        // Check if user is authenticated and has teacher role
        if (!$user || $user->role !== 'teacher') {
            abort(403, 'Access denied. Teacher role required.');
        }
        
        // Block POST, PUT, PATCH, DELETE requests for teachers (view-only access)
        if (in_array($request->method(), ['POST', 'PUT', 'PATCH', 'DELETE'])) {
            abort(403, 'Teachers have view-only access.');
        }
        
        return $next($request);
    }
}
