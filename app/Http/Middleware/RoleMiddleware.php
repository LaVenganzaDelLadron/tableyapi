<?php

namespace App\Http\Middleware;

use Closure;
use Illuminate\Http\Request;
use Symfony\Component\HttpFoundation\Response;

class RoleMiddleware
{
    /**
     * Handle an incoming request.
     */
    public function handle(Request $request, Closure $next, string ...$roles): Response
    {
        $user = $request->user();

        if (! $user) {
            return $this->deny('Unauthenticated.', 401);
        }

        if ($roles === []) {
            return $this->deny('Role access is not configured.', 403);
        }

        if (! in_array($user->role, $roles, true)) {
            return $this->deny('Unauthorized access.', 403);
        }

        return $next($request);
    }

    private function deny(string $message, int $status): Response
    {
        return response()->json([
            'status' => 'error',
            'message' => $message,
        ], $status);
    }
}
