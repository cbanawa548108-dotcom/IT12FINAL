<?php

namespace App\Http\Middleware;

use Closure;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Cache;
use App\Models\BlockedIp;

class TrackFailedLogins
{
    protected int $maxAttempts = 5;
    protected int $blockMinutes = 30;

    public function handle(Request $request, Closure $next)
    {
        $response = $next($request);

        // Only track POST to login route
        if ($request->isMethod('POST') && $request->routeIs('login')) {
            $ip = $request->ip();
            $key = "failed_logins:{$ip}";

            // Detect failed login (session has errors or auth failed)
            $loginFailed = $response->getStatusCode() === 302
                && session()->has('errors')
                && !auth()->check();

            if ($loginFailed) {
                $attempts = Cache::get($key, 0) + 1;
                Cache::put($key, $attempts, now()->addMinutes(60));

                if ($attempts >= $this->maxAttempts) {
                    BlockedIp::updateOrCreate(
                        ['ip_address' => $ip],
                        [
                            'reason' => "Too many failed login attempts ({$attempts}x).",
                            'blocked_until' => now()->addMinutes($this->blockMinutes),
                        ]
                    );
                    Cache::forget($key);
                }
            }
        }

        return $response;
    }
}