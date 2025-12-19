<?php

namespace App\Http\Middleware;

use Closure;
use Illuminate\Http\Request;
use Symfony\Component\HttpFoundation\Response;

class RoleMiddleware
{
    /**
     * Handle an incoming request.
     *
     * @param  \Closure(\Illuminate\Http\Request): (\Symfony\Component\HttpFoundation\Response)  $next
     */
    public function handle(Request $request, Closure $next, string ...$roles): Response
    {
        $user = $request->user();

        if (! $user) {
            abort(403);
        }

        // Nếu có roles được chỉ định, kiểm tra quyền
        if (! empty($roles)) {
            // Cho phép 'agent' truy cập các route yêu cầu 'admin'
            $allowedRoles = $roles;
            if (in_array('admin', $roles, true)) {
                $allowedRoles[] = 'agent';
            }
            
            if (! in_array($user->role, $allowedRoles, true)) {
                abort(403);
            }
        }

        return $next($request);
    }
}


