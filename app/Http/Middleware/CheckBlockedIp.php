<?php

namespace App\Http\Middleware;

use Closure;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;
use App\Models\BlockedIp;

class CheckBlockedIp
{
    public function handle(Request $request, Closure $next)
    {
        $ip     = $request->ip();
        $userId = Auth::check() ? Auth::user()->User_ID : null;

        // ── Check user-specific block first ───────────────────
        if ($userId) {
            $blocked = BlockedIp::where('user_id', $userId)->first();

            if ($blocked) {
                if ($blocked->isExpired()) {
                    $blocked->delete();
                } else {
                    Auth::logout();
                    return response()->view('errors.blocked', [
                        'reason'        => $blocked->reason ?? 'Your account has been blocked.',
                        'blocked_until' => $blocked->blocked_until,
                    ], 403);
                }
            }
        }

        // ── Check IP-only block (no user_id = applies to all on that IP) ──
        $ipBlocked = BlockedIp::whereNull('user_id')
            ->where('ip_address', $ip)
            ->first();

        if ($ipBlocked) {
            if ($ipBlocked->isExpired()) {
                $ipBlocked->delete();
            } else {
                return response()->view('errors.blocked', [
                    'reason'        => $ipBlocked->reason ?? 'Your IP has been blocked.',
                    'blocked_until' => $ipBlocked->blocked_until,
                ], 403);
            }
        }

        return $next($request);
    }
}