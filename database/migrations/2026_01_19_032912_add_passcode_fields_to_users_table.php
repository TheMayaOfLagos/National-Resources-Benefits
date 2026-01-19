<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    /**
     * Run the migrations.
     */
    public function up(): void
    {
        Schema::table('users', function (Blueprint $table) {
            // Withdrawal passcode (hashed 6-digit PIN)
            $table->string('withdrawal_passcode')->nullable()->after('withdrawal_message');
            $table->timestamp('withdrawal_passcode_set_at')->nullable()->after('withdrawal_passcode');

            // Admin control: require passcode for withdrawal (default true for all users)
            $table->boolean('require_withdrawal_passcode')->default(true)->after('withdrawal_passcode_set_at');

            // Withdrawal OTP for email verification fallback
            $table->string('withdrawal_otp', 6)->nullable()->after('require_withdrawal_passcode');
            $table->timestamp('withdrawal_otp_expires_at')->nullable()->after('withdrawal_otp');

            // Track failed passcode attempts for security
            $table->unsignedTinyInteger('passcode_failed_attempts')->default(0)->after('withdrawal_otp_expires_at');
            $table->timestamp('passcode_locked_until')->nullable()->after('passcode_failed_attempts');
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::table('users', function (Blueprint $table) {
            $table->dropColumn([
                'withdrawal_passcode',
                'withdrawal_passcode_set_at',
                'require_withdrawal_passcode',
                'withdrawal_otp',
                'withdrawal_otp_expires_at',
                'passcode_failed_attempts',
                'passcode_locked_until',
            ]);
        });
    }
};
