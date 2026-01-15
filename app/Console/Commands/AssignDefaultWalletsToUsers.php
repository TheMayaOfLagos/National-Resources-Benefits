<?php

namespace App\Console\Commands;

use App\Models\User;
use App\Models\WalletType;
use Illuminate\Console\Command;

class AssignDefaultWalletsToUsers extends Command
{
    /**
     * The name and signature of the console command.
     *
     * @var string
     */
    protected $signature = 'users:assign-wallets {--user= : Specific user ID to assign wallets to}';

    /**
     * The console command description.
     *
     * @var string
     */
    protected $description = 'Assign the first two default wallet types to users who don\'t have them';

    /**
     * Execute the console command.
     */
    public function handle()
    {
        // Get the first two active wallet types
        $defaultWallets = WalletType::where('is_active', true)
            ->orderBy('display_order')
            ->orderBy('id')
            ->take(2)
            ->get();

        if ($defaultWallets->count() < 2) {
            $this->error('Not enough active wallet types found. Please create at least 2 wallet types.');
            return 1;
        }

        $this->info('Default wallets to assign: ' . $defaultWallets->pluck('name')->join(', '));

        // Get users to process
        $query = User::query();
        
        if ($userId = $this->option('user')) {
            $query->where('id', $userId);
        }

        $users = $query->get();
        $assigned = 0;

        $this->withProgressBar($users, function ($user) use ($defaultWallets, &$assigned) {
            foreach ($defaultWallets as $walletType) {
                // Check if user already has this wallet type
                $exists = $user->accounts()
                    ->where('wallet_type_id', $walletType->id)
                    ->exists();

                if (!$exists) {
                    $user->accounts()->create([
                        'wallet_type_id' => $walletType->id,
                        'account_number' => $this->generateAccountNumber($walletType),
                        'currency' => $walletType->currency_code,
                        'balance' => 0,
                        'account_type' => 'checking',
                    ]);
                    $assigned++;
                }
            }
        });

        $this->newLine(2);
        $this->info("Assigned {$assigned} wallet(s) to users.");

        return 0;
    }

    /**
     * Generate a unique account number for the wallet.
     */
    private function generateAccountNumber(WalletType $walletType): string
    {
        $prefix = strtoupper(substr($walletType->slug, 0, 3));
        return $prefix . '-' . strtoupper(uniqid()) . '-' . rand(1000, 9999);
    }
}
