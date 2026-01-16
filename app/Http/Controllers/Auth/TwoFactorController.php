<?php

namespace App\Http\Controllers\Auth;

use App\Http\Controllers\Controller;
use App\Models\Setting;
use Illuminate\Http\RedirectResponse;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Hash;
use Illuminate\Support\Str;
use Inertia\Inertia;
use Inertia\Response;
use PragmaRX\Google2FA\Google2FA;

class TwoFactorController extends Controller
{
    protected Google2FA $google2fa;

    public function __construct()
    {
        $this->google2fa = new Google2FA();
    }

    /**
     * Display the 2FA challenge page during login.
     */
    public function show(Request $request): Response|RedirectResponse
    {
        $user = $request->user();

        // If 2FA is not enabled, redirect to dashboard
        if (!$user->two_factor_enabled || !$user->two_factor_secret) {
            return redirect()->intended(route('dashboard', absolute: false));
        }

        // If already verified in this session
        if (session('two_factor_verified')) {
            return redirect()->intended(route('dashboard', absolute: false));
        }

        return Inertia::render('Auth/TwoFactorChallenge', [
            'email' => $this->maskEmail($user->email),
        ]);
    }

    /**
     * Verify the 2FA code during login.
     */
    public function verify(Request $request): RedirectResponse
    {
        $request->validate([
            'code' => ['required', 'string'],
        ]);

        $user = $request->user();
        $code = $request->code;

        // Check if it's a recovery code
        if (strlen($code) > 6) {
            return $this->verifyRecoveryCode($user, $code);
        }

        // Verify TOTP code
        $valid = $this->google2fa->verifyKey(
            decrypt($user->two_factor_secret),
            $code
        );

        if (!$valid) {
            return back()->withErrors(['code' => 'The provided two-factor authentication code was invalid.']);
        }

        // Mark as verified in session
        session(['two_factor_verified' => true]);

        return redirect()->intended(route('dashboard', absolute: false));
    }

    /**
     * Verify a recovery code.
     */
    protected function verifyRecoveryCode($user, string $code): RedirectResponse
    {
        $recoveryCodes = $user->two_factor_recovery_codes ?? [];

        if (!in_array($code, $recoveryCodes)) {
            return back()->withErrors(['code' => 'The provided recovery code was invalid.']);
        }

        // Remove used recovery code
        $user->update([
            'two_factor_recovery_codes' => array_values(array_diff($recoveryCodes, [$code])),
        ]);

        // Mark as verified in session
        session(['two_factor_verified' => true]);

        return redirect()->intended(route('dashboard', absolute: false));
    }

    /**
     * Enable 2FA - Generate secret and show QR code.
     */
    public function enable(Request $request): RedirectResponse
    {
        $request->validate([
            'password' => ['required', 'current_password'],
        ]);

        $user = $request->user();

        // Generate secret if not exists
        if (!$user->two_factor_secret) {
            $secret = $this->google2fa->generateSecretKey();
            $user->update([
                'two_factor_secret' => encrypt($secret),
            ]);
        } else {
            $secret = decrypt($user->two_factor_secret);
        }

        // Generate QR code URL
        $siteName = Setting::get('site_name', 'NationalResourceBenefits');
        $qrCodeUrl = $this->google2fa->getQRCodeUrl(
            $siteName,
            $user->email,
            $secret
        );

        return back()->with([
            'secret' => $secret,
            'qr_code_url' => $qrCodeUrl,
        ]);
    }

    /**
     * Confirm 2FA setup with verification code.
     */
    public function confirm(Request $request): RedirectResponse
    {
        $request->validate([
            'code' => ['required', 'string', 'size:6'],
        ]);

        $user = $request->user();

        if (!$user->two_factor_secret) {
            return back()->withErrors(['code' => 'Two-factor authentication has not been enabled.']);
        }

        $valid = $this->google2fa->verifyKey(
            decrypt($user->two_factor_secret),
            $request->code
        );

        if (!$valid) {
            return back()->withErrors(['code' => 'The provided code was invalid.']);
        }

        // Generate recovery codes
        $recoveryCodes = $this->generateRecoveryCodes();

        $user->update([
            'two_factor_enabled' => true,
            'two_factor_confirmed_at' => now(),
            'two_factor_recovery_codes' => $recoveryCodes,
        ]);

        return back()->with('recovery_codes', $recoveryCodes);
    }

    /**
     * Disable 2FA.
     */
    public function disable(Request $request): RedirectResponse
    {
        $request->validate([
            'password' => ['required', 'current_password'],
        ]);

        $user = $request->user();

        $user->update([
            'two_factor_secret' => null,
            'two_factor_recovery_codes' => null,
            'two_factor_confirmed_at' => null,
            'two_factor_enabled' => false,
        ]);

        return back()->with('status', 'two-factor-disabled');
    }

    /**
     * Regenerate recovery codes.
     */
    public function regenerateRecoveryCodes(Request $request): RedirectResponse
    {
        $request->validate([
            'password' => ['required', 'current_password'],
        ]);

        $user = $request->user();

        if (!$user->two_factor_enabled) {
            return back()->withErrors(['password' => 'Two-factor authentication is not enabled.']);
        }

        $recoveryCodes = $this->generateRecoveryCodes();

        $user->update([
            'two_factor_recovery_codes' => $recoveryCodes,
        ]);

        return back()->with('recovery_codes', $recoveryCodes);
    }

    /**
     * Generate recovery codes.
     */
    protected function generateRecoveryCodes(): array
    {
        $codes = [];
        for ($i = 0; $i < 8; $i++) {
            $codes[] = Str::random(10) . '-' . Str::random(10);
        }
        return $codes;
    }

    /**
     * Mask email for display.
     */
    protected function maskEmail(string $email): string
    {
        $parts = explode('@', $email);
        $name = $parts[0];
        $domain = $parts[1] ?? '';

        if (strlen($name) <= 2) {
            $maskedName = $name[0] . '***';
        } else {
            $maskedName = $name[0] . str_repeat('*', strlen($name) - 2) . substr($name, -1);
        }

        return $maskedName . '@' . $domain;
    }
}
