<?php

namespace App\Http\Controllers\Auth;

use App\Http\Controllers\Controller;
use Illuminate\Auth\Events\Verified;
use Illuminate\Http\RedirectResponse;
use Illuminate\Http\Request;
use Inertia\Inertia;
use Inertia\Response;

class EmailVerificationCodeController extends Controller
{
    /**
     * Verify the email using the provided code.
     */
    public function verify(Request $request)
    {
        $request->validate([
            'code' => ['required', 'string', 'size:6'],
        ]);

        $user = $request->user();

        if ($user->hasVerifiedEmail()) {
            return redirect()->route('dashboard')->with('verified', true);
        }

        if (!$user->verifyEmailCode($request->code)) {
            return back()->withErrors(['code' => 'The verification code is invalid or has expired.']);
        }

        event(new Verified($user));

        // Check if login OTP is required and not verified
        $loginOtpEnabled = \App\Models\Setting::get('login_otp_enabled', true);
        if ($loginOtpEnabled && !$user->login_otp_verified) {
            return redirect()->route('auth.login-otp.show');
        }

        // Check if ID.me verification is required
        if (config('services.idme.required', false) && !$user->idme_verified_at) {
            return redirect()->route('auth.idme.verify');
        }

        return redirect()->route('dashboard')->with('verified', true);
    }

    /**
     * Resend the verification code.
     */
    public function resend(Request $request): RedirectResponse
    {
        if ($request->user()->hasVerifiedEmail()) {
            return redirect()->intended(route('dashboard', absolute: false));
        }

        $request->user()->sendEmailVerificationNotification();

        return back()->with('status', 'verification-code-sent');
    }
}
