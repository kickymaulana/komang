<?php

namespace App\Http\Middleware;

use Carbon\Carbon;
use Closure;
use Illuminate\Http\Request;
use Symfony\Component\HttpFoundation\Response;

class CheckAppExpiry
{
    /**
     * Blokir seluruh aplikasi jika sewa bulanan sudah habis.
     *
     * @param  Closure(Request): (Response)  $next
     */
    public function handle(Request $request, Closure $next): Response
    {
        if (! config('app.expiry_enabled') || ! config('app.expired_at')) {
            return $next($request);
        }

        try {
            $expiredAt = Carbon::parse(config('app.expired_at'));
        } catch (\Throwable) {
            return $next($request);
        }

        if (now()->lessThan($expiredAt)) {
            return $next($request);
        }

        return response()->view('expired', [
            'message' => config('app.expiry_message'),
            'expiredAt' => $expiredAt->format('d-m-Y'),
        ], 403);
    }
}
