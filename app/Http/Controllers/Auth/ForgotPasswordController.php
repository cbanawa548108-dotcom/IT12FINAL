<?php

namespace App\Http\Controllers\Auth;

use App\Http\Controllers\Controller;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Hash;
use Illuminate\Support\Facades\Mail;
use Illuminate\Support\Facades\Log;
use App\Models\User;
use App\Models\PasswordChangeLog;
use Carbon\Carbon;

class ForgotPasswordController extends Controller
{
    // ─────────────────────────────────────────────────────────
    // Show forgot password form
    // ─────────────────────────────────────────────────────────

    public function showForgotForm()
    {
        return view('auth.forgot-password');
    }

    // ─────────────────────────────────────────────────────────
    // Send reset code to email
    // ─────────────────────────────────────────────────────────

    public function sendResetCode(Request $request)
    {
        $request->validate([
            'email' => 'required|email|exists:users,email'
        ], [
            'email.exists' => 'We could not find a user with that email address.'
        ]);

        $code = rand(100000, 999999);

        DB::table('password_reset_tokens')->where('email', $request->email)->delete();

        DB::table('password_reset_tokens')->insert([
            'email'      => $request->email,
            'token'      => Hash::make($code),
            'created_at' => Carbon::now()
        ]);

        try {
            Mail::send('emails.reset-password', ['code' => $code], function ($message) use ($request) {
                $message->to($request->email);
                $message->subject('Password Reset Code - CRM FruitStand');
            });

            return redirect()->route('password.reset', ['email' => $request->email])
                ->with('success', 'Reset code sent to your email!');

        } catch (\Exception $e) {
            Log::error('Email sending error: ' . $e->getMessage());
            return back()->withErrors(['email' => 'Failed to send email. Please try again later.']);
        }
    }

    // ─────────────────────────────────────────────────────────
    // Show reset password form
    // ─────────────────────────────────────────────────────────

    public function showResetForm(Request $request)
    {
        return view('auth.reset-password')->with('email', $request->email);
    }

    // ─────────────────────────────────────────────────────────
    // Update password with code verification
    // ─────────────────────────────────────────────────────────

    public function updatePassword(Request $request)
    {
        $request->validate([
            'email'    => 'required|email|exists:users,email',
            'code'     => 'required|numeric|digits:6',
            'password' => [
                'required',
                'string',
                'min:12',
                'max:16',
                'confirmed',
                'regex:/[A-Z]/',
                'regex:/[a-z]/',
                'regex:/[0-9]/',
                'regex:/[!@#$%^&*()\-_=+\[\]{};\':"\\|,.<>\/?]/',
            ],
        ], [
            'code.digits'        => 'The reset code must be 6 digits.',
            'password.confirmed' => 'The passwords do not match.',
            'password.min'       => 'Password must be at least 12 characters.',
            'password.max'       => 'Password must not exceed 16 characters.',
            'password.regex'     => 'Password must include uppercase, lowercase, a number, and a special character.',
        ]);

        // ── Check token exists ────────────────────────────────
        $passwordReset = DB::table('password_reset_tokens')
            ->where('email', $request->email)
            ->first();

        if (!$passwordReset) {
            return back()->withErrors(['code' => 'Invalid or expired reset code.']);
        }

        // ── Check code matches ────────────────────────────────
        if (!Hash::check($request->code, $passwordReset->token)) {
            return back()->withErrors(['code' => 'Invalid reset code.']);
        }

        // ── Check expiry (30 minutes) ─────────────────────────
        if (Carbon::parse($passwordReset->created_at)->addMinutes(30)->isPast()) {
            DB::table('password_reset_tokens')->where('email', $request->email)->delete();
            return back()->withErrors(['code' => 'Reset code has expired. Please request a new one.']);
        }

        // ── Update password ───────────────────────────────────
        $user = User::where('email', $request->email)->first();
        $user->password = Hash::make($request->password);
        $user->save();

        // ── Delete token ──────────────────────────────────────
        DB::table('password_reset_tokens')->where('email', $request->email)->delete();

        // ── Log password change ───────────────────────────────
        PasswordChangeLog::create([
            'user_id'    => $user->User_ID, // ✅ fixed: use User_ID not id
            'ip_address' => $request->ip(),
            'user_agent' => $request->userAgent(),
        ]);

        return redirect()->route('login')
            ->with('success', 'Password reset successfully! You can now login with your new password.');
    }
}