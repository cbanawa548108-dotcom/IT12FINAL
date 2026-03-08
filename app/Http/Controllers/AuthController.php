<?php

namespace App\Http\Controllers;

use Illuminate\Http\Request;
use App\Models\User;
use App\Models\LoginAuditLog;
use App\Models\BlockedIp;
use App\Mail\TwoFactorCodeMail;
use Illuminate\Support\Facades\Hash;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\Mail;
use Illuminate\Support\Facades\RateLimiter;
use Illuminate\Support\Facades\Http;
use Illuminate\Validation\ValidationException;

class AuthController extends Controller
{
    // ─────────────────────────────────────────────────────────
    // STAGE 1 — Show login form
    // ─────────────────────────────────────────────────────────

    public function showLogin()
    {
        return view('auth.login');
    }

    // ─────────────────────────────────────────────────────────
    // STAGE 1 — Handle credentials
    // ─────────────────────────────────────────────────────────

    public function login(Request $request)
    {
        $request->validate([
            'email'    => 'required|email',
            'password' => 'required',
        ]);

        $throttleKey = 'login-attempt:' . strtolower($request->email);

        // ── Rate limit check ──────────────────────────────────
        if (RateLimiter::tooManyAttempts($throttleKey, 5)) {
            $seconds = RateLimiter::availableIn($throttleKey);
            $minutes = ceil($seconds / 60);

            $this->logLoginAttempt(
                $request,
                'locked',
                'account_locked',
                RateLimiter::attempts($throttleKey)
            );

            // ── Auto-block the IP for 30 minutes ─────────────
            BlockedIp::updateOrCreate(
                ['ip_address' => $request->ip()],
                [
                    'reason'        => 'Auto-blocked: 5 failed login attempts.',
                    'blocked_until' => now()->addMinutes(30),
                    'blocked_by'    => null,
                ]
            );

            throw ValidationException::withMessages([
                'email' => "Too many login attempts. Please try again in {$minutes} minute(s).",
            ]);
        }

        // ── Step 1: Check if email exists ─────────────────────
        $user = User::where('email', $request->email)->first();

        if (!$user) {
            RateLimiter::hit($throttleKey, 900);

            $this->logLoginAttempt(
                $request,
                'failed',
                'email_not_found',
                RateLimiter::attempts($throttleKey),
                null
            );

            $attemptsLeft = 5 - RateLimiter::attempts($throttleKey);

            return back()->withErrors([
                'email' => $attemptsLeft > 0
                    ? "No account found with this email address. {$attemptsLeft} attempt(s) remaining."
                    : 'No account found with this email address.',
            ])->withInput($request->only('email'));
        }

        // ── Step 2: Check password ────────────────────────────
        if (!Hash::check($request->password, $user->password)) {
            RateLimiter::hit($throttleKey, 900);

            $this->logLoginAttempt(
                $request,
                'failed',
                'wrong_password',
                RateLimiter::attempts($throttleKey),
                $user->User_ID
            );

            $attemptsLeft = 5 - RateLimiter::attempts($throttleKey);

            return back()->withErrors([
                'password' => $attemptsLeft > 0
                    ? "Incorrect password. You have {$attemptsLeft} attempt(s) remaining."
                    : 'Incorrect password.',
            ])->withInput($request->only('email'));
        }

        // ── Step 3: Credentials valid — proceed to 2FA ───────
        $code = $user->generateTwoFactorCode();

        Mail::to($user->email)->send(new TwoFactorCodeMail($code, $user->fname));

        session([
            'two_factor_user_id' => $user->User_ID,
            'two_factor_email'   => $user->email,
        ]);

        return redirect()->route('two-factor.show');
    }

    // ─────────────────────────────────────────────────────────
    // STAGE 2 — Show 2FA form
    // ─────────────────────────────────────────────────────────

    public function showTwoFactor()
    {
        if (!session('two_factor_user_id')) {
            return redirect()->route('login');
        }

        return view('auth.two-factor');
    }

    // ─────────────────────────────────────────────────────────
    // STAGE 2 — Verify OTP
    // ─────────────────────────────────────────────────────────

    public function verifyTwoFactor(Request $request)
    {
        $request->validate([
            'code' => 'required|digits:6',
        ]);

        if (!session('two_factor_user_id')) {
            return redirect()->route('login');
        }

        $user = User::find(session('two_factor_user_id'));

        if (!$user) {
            return redirect()->route('login')->withErrors(['code' => 'Session expired. Please log in again.']);
        }

        if (!$user->isValidTwoFactorCode($request->code)) {
            return back()->withErrors(['code' => 'Invalid or expired code. Please try again.']);
        }

        $user->clearTwoFactorCode();

        session(['two_factor_passed' => true]);
        session()->forget('two_factor_user_id');

        return redirect()->route('captcha.show');
    }

    // ─────────────────────────────────────────────────────────
    // STAGE 2 — Resend OTP
    // ─────────────────────────────────────────────────────────

    public function resendTwoFactor(Request $request)
    {
        if (!session('two_factor_user_id')) {
            return redirect()->route('login');
        }

        $user = User::find(session('two_factor_user_id'));

        if ($user) {
            $code = $user->generateTwoFactorCode();
            Mail::to($user->email)->send(new TwoFactorCodeMail($code, $user->fname));
        }

        return back()->with('success', 'A new code has been sent to your email.');
    }

    // ─────────────────────────────────────────────────────────
    // STAGE 3 — Show CAPTCHA form
    // ─────────────────────────────────────────────────────────

    public function showCaptcha()
    {
        if (!session('two_factor_passed') || !session('two_factor_email')) {
            return redirect()->route('login');
        }

        return view('auth.captcha');
    }

    // ─────────────────────────────────────────────────────────
    // STAGE 3 — Verify CAPTCHA and complete login
    // ─────────────────────────────────────────────────────────

    public function verifyCaptcha(Request $request)
    {
        if (!session('two_factor_passed') || !session('two_factor_email')) {
            return redirect()->route('login');
        }

        $recaptchaSecret   = config('services.recaptcha.secret');
        $recaptchaResponse = $request->input('g-recaptcha-response');

        if (!$recaptchaResponse) {
            return back()->withErrors(['captcha' => 'Please complete the reCAPTCHA verification.']);
        }

        $response = Http::asForm()->post('https://www.google.com/recaptcha/api/siteverify', [
            'secret'   => $recaptchaSecret,
            'response' => $recaptchaResponse,
            'remoteip' => $request->ip(),
        ]);

        $responseData = $response->json();

        if (!$responseData['success']) {
            $this->logLoginAttempt($request, 'failed', 'captcha_failed');
            return back()->withErrors(['captcha' => 'reCAPTCHA verification failed. Please try again.']);
        }

        $user = User::where('email', session('two_factor_email'))->first();

        if (!$user) {
            return redirect()->route('login')->withErrors(['email' => 'User not found. Please log in again.']);
        }

        $throttleKey = 'login-attempt:' . strtolower($user->email);
        RateLimiter::clear($throttleKey);

        $this->logLoginAttempt($request, 'success', null, 0, $user->User_ID);

        session()->forget(['two_factor_passed', 'two_factor_email']);

        Auth::login($user);

        return redirect()->route('dashboard');
    }

    // ─────────────────────────────────────────────────────────
    // Logout
    // ─────────────────────────────────────────────────────────

    public function logout()
    {
        Auth::logout();
        return redirect()->route('login');
    }

    // ─────────────────────────────────────────────────────────
    // Force Logout (tab/browser close)
    // ─────────────────────────────────────────────────────────

    public function forceLogout()
    {
        Auth::logout();
        session()->invalidate();
        session()->regenerateToken();
        return response()->json(['success' => true]);
    }

    // ─────────────────────────────────────────────────────────
    // Audit Log
    // ─────────────────────────────────────────────────────────

    private function logLoginAttempt(
        Request $request,
        string  $status,
        ?string $failureReason = null,
        int     $attemptsCount = 0,
        ?int    $userId = null
    ): void {
        LoginAuditLog::create([
            'email'          => strtolower($request->input('email', session('two_factor_email', ''))),
            'user_id'        => $userId,
            'status'         => $status,
            'ip_address'     => $request->ip(),
            'user_agent'     => $request->userAgent(),
            'failure_reason' => $failureReason,
            'attempts_count' => $attemptsCount,
            'locked_until'   => $status === 'locked' ? now()->addMinutes(15) : null,
        ]);
    }

    public function loginAuditLogs()
    {
        $auditLogs = LoginAuditLog::with('user')
            ->orderBy('created_at', 'desc')
            ->paginate(20);

        return view('auth.login-audit', compact('auditLogs'));
    }
}