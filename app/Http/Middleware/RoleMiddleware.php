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
     public function handle(Request $request, Closure $next, $role)
    {
        $user = $request->user();

        if (!$user) {
            return response()->json(['message' => 'Forbidden'], 403);
        }

        // Allow multiple roles separated by comma
        $roles = explode(',', $role);
        foreach ($roles as $r) {
            if ($user->hasRole(trim($r))) {
                return $next($request);
            }
        }

        return response()->json(['message' => 'Forbidden'], 403);
    }
}
