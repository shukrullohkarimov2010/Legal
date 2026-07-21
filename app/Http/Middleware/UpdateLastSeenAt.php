<?php

namespace App\Http\Middleware;

use Closure;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Schema;
use Symfony\Component\HttpFoundation\Response;

class UpdateLastSeenAt
{
    /**
     * Handle an incoming request.
     *
     * @param  \Closure(\Illuminate\Http\Request): (\Symfony\Component\HttpFoundation\Response)  $next
     */
    public function handle(Request $request, Closure $next): Response
    {
        if (auth()->check() && Schema::hasColumn('users', 'last_seen_at')) {
            $user = auth()->user();
            // Update last_seen_at only if more than 5 minutes have passed
            if (!$user->last_seen_at || $user->last_seen_at->diffInMinutes(now()) >= 5) {
                $user->update(['last_seen_at' => now()]);
            }
        }

        return $next($request);
    }
}
