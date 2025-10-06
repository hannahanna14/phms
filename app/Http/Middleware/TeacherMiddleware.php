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
        
        // Allow teachers to manage chat only, block other modifications
        $allowedRoutes = [
            'chat',
            'messages'
        ];
        
        $currentRoute = $request->route() ? $request->route()->getName() : '';
        $currentPath = $request->path();
        
        // Check if the request is for an allowed route
        $isAllowedRoute = false;
        foreach ($allowedRoutes as $allowedRoute) {
            if (str_contains($currentRoute, $allowedRoute) || str_contains($currentPath, $allowedRoute)) {
                $isAllowedRoute = true;
                break;
            }
        }
        
        // Block POST, PUT, PATCH, DELETE requests for teachers except for allowed routes
        if (in_array($request->method(), ['POST', 'PUT', 'PATCH', 'DELETE']) && !$isAllowedRoute) {
            abort(403, 'Teachers have view-only access to this resource.');
        }
        
        return $next($request);
    }
}
