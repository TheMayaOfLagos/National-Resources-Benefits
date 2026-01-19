<?php

namespace App\Http\Controllers;

use App\Notifications\WithdrawalOtpNotification;
use Illuminate\Http\JsonResponse;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;

class PasscodeController extends Controller
{
    /**
     * Get passcode status for the authenticated user.
     */
    public function status(Request $request): JsonResponse
    {
        $user = $request->user();

        return response()->json([
            'has_passcode' => $user->hasWithdrawalPasscode(),
            'requires_passcode' => $user->requiresWithdrawalPasscode(),
            'is_locked' => $user->isPasscodeLocked(),
            'lockout_remaining' => $user->isPasscodeLocked()
                ? $user->getPasscodeLockoutRemaining()
                : 0,
        ]);
    }

    /**
     * Set up or update the withdrawal passcode.
     */
    public function setup(Request $request): JsonResponse
    {
        $validated = $request->validate([
            'passcode' => ['required', 'string', 'size:6', 'regex:/^[0-9]{6}$/'],
            'passcode_confirmation' => ['required', 'same:passcode'],
            'current_passcode' => ['nullable', 'string', 'size:6'],
        ]);

        $user = $request->user();

        // If user already has a passcode, verify current passcode first
        if ($user->hasWithdrawalPasscode()) {
            if (empty($validated['current_passcode'])) {
                return response()->json([
                    'success' => false,
                    'message' => 'Current passcode is required to change your passcode.',
                ], 422);
            }

            if (!$user->verifyWithdrawalPasscode($validated['current_passcode'])) {
                return response()->json([
                    'success' => false,
                    'message' => 'Current passcode is incorrect.',
                ], 422);
            }
        }

        // Set the new passcode
        $user->setWithdrawalPasscode($validated['passcode']);

        return response()->json([
            'success' => true,
            'message' => 'Withdrawal passcode has been set successfully.',
        ]);
    }

    /**
     * Verify the passcode for withdrawal.
     */
    public function verify(Request $request): JsonResponse
    {
        $validated = $request->validate([
            'passcode' => ['required', 'string', 'size:6'],
        ]);

        $user = $request->user();

        // Check if account is locked
        if ($user->isPasscodeLocked()) {
            $remaining = $user->getPasscodeLockoutRemaining();
            return response()->json([
                'success' => false,
                'locked' => true,
                'lockout_remaining' => $remaining,
                'message' => "Account is temporarily locked. Please try again in {$remaining} minutes.",
            ], 423);
        }

        // Verify the passcode
        if (!$user->verifyWithdrawalPasscode($validated['passcode'])) {
            $attemptsLeft = 5 - $user->passcode_failed_attempts;

            return response()->json([
                'success' => false,
                'message' => 'Incorrect passcode.',
                'attempts_remaining' => max(0, $attemptsLeft),
                'locked' => $user->isPasscodeLocked(),
            ], 401);
        }

        // Passcode verified successfully
        return response()->json([
            'success' => true,
            'message' => 'Passcode verified successfully.',
        ]);
    }

    /**
     * Send OTP to user's email as an alternative to passcode.
     */
    public function sendOtp(Request $request): JsonResponse
    {
        $user = $request->user();

        // Check if account is locked
        if ($user->isPasscodeLocked()) {
            $remaining = $user->getPasscodeLockoutRemaining();
            return response()->json([
                'success' => false,
                'locked' => true,
                'lockout_remaining' => $remaining,
                'message' => "Account is temporarily locked. Please try again in {$remaining} minutes.",
            ], 423);
        }

        // Generate and save OTP
        $otp = $user->generateWithdrawalOtp();

        // Send OTP via email
        $user->notify(new WithdrawalOtpNotification($otp));

        return response()->json([
            'success' => true,
            'message' => 'OTP has been sent to your email address.',
            'expires_in' => 10, // minutes
        ]);
    }

    /**
     * Verify the email OTP for withdrawal.
     */
    public function verifyOtp(Request $request): JsonResponse
    {
        $validated = $request->validate([
            'otp' => ['required', 'string', 'size:6'],
        ]);

        $user = $request->user();

        // Check if account is locked
        if ($user->isPasscodeLocked()) {
            $remaining = $user->getPasscodeLockoutRemaining();
            return response()->json([
                'success' => false,
                'locked' => true,
                'lockout_remaining' => $remaining,
                'message' => "Account is temporarily locked. Please try again in {$remaining} minutes.",
            ], 423);
        }

        // Verify the OTP
        if (!$user->verifyWithdrawalOtp($validated['otp'])) {
            return response()->json([
                'success' => false,
                'message' => 'Invalid or expired OTP.',
            ], 401);
        }

        return response()->json([
            'success' => true,
            'message' => 'OTP verified successfully.',
        ]);
    }

    /**
     * Remove/disable the withdrawal passcode.
     */
    public function remove(Request $request): JsonResponse
    {
        $validated = $request->validate([
            'passcode' => ['required', 'string', 'size:6'],
        ]);

        $user = $request->user();

        // Verify current passcode
        if (!$user->verifyWithdrawalPasscode($validated['passcode'])) {
            return response()->json([
                'success' => false,
                'message' => 'Incorrect passcode.',
            ], 401);
        }

        // Remove passcode
        $user->update([
            'withdrawal_passcode' => null,
            'withdrawal_passcode_set_at' => null,
        ]);

        return response()->json([
            'success' => true,
            'message' => 'Withdrawal passcode has been removed.',
        ]);
    }
}
