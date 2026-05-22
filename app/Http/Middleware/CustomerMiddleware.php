<?php

namespace App\Http\Middleware;

use Closure;
use Illuminate\Http\Request;
use Symfony\Component\HttpFoundation\Response;

class CustomerMiddleware
{
    /**
     * Handle an incoming request.
     */
    public function handle(Request $request, Closure $next): Response
    {
        $user = $request->user();

        if (! $user) {
            return $this->deny('Unauthenticated.', 401);
        }

        if (! in_array($user->role, ['customer', 'admin'], true)) {
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
