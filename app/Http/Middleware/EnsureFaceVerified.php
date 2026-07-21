<?php

namespace App\Http\Middleware;

use Closure;
use Illuminate\Http\Request;
use Symfony\Component\HttpFoundation\Response;

class EnsureFaceVerified
{
    /**
     * @param  \Closure(\Illuminate\Http\Request): (\Symfony\Component\HttpFoundation\Response)  $next
     */
    public function handle(Request $request, Closure $next): Response
    {
        if (
            $request->session()->has('face_verified_at')
            && (int) $request->session()->get('face_verified_user_id', 0) === (int) $request->user()?->id
        ) {
            return $next($request);
        }

        $request->session()->forget([
            'face_verified_at',
            'face_verified_user_id',
        ]);
        $request->session()->put('url.intended', $request->fullUrl());

        return redirect()->route('face.verification');
    }
}
