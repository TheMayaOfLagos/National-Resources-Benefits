<?php

namespace App\Observers;

use App\Models\User;
use App\Models\WalletType;

class UserObserver
{
    /**
     * Handle the User "created" event.
     * Auto-assigns the first two active wallet types to new users.
     */
    public function created(User $user): void
    {
        // Get the first two active wallet types ordered by display_order
        // These are the default wallets assigned to all users
        $defaultWallets = WalletType::where('is_active', true)
            ->orderBy('display_order')
            ->orderBy('id')
            ->take(2)
            ->get();

        foreach ($defaultWallets as $walletType) {
            $user->accounts()->create([
                'wallet_type_id' => $walletType->id,
                'account_number' => $this->generateAccountNumber($walletType),
                'currency' => $walletType->currency_code,
                'balance' => 0,
                'account_type' => 'checking',
            ]);
        }
    }

    /**
     * Generate a unique account number for the wallet.
     */
    private function generateAccountNumber(WalletType $walletType): string
    {
        $prefix = strtoupper(substr($walletType->slug, 0, 3));
        return $prefix . '-' . strtoupper(uniqid()) . '-' . rand(1000, 9999);
    }

    /**
     * Handle the User "updated" event.
     */
    public function updated(User $user): void
    {
        //
    }

    /**
     * Handle the User "deleted" event.
     */
    public function deleted(User $user): void
    {
        //
    }

    /**
     * Handle the User "restored" event.
     */
    public function restored(User $user): void
    {
        //
    }

    /**
     * Handle the User "force deleted" event.
     */
    public function forceDeleted(User $user): void
    {
        //
    }
}
