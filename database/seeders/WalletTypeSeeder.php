<?php

namespace Database\Seeders;

use App\Models\WalletType;
use Illuminate\Database\Seeder;

class WalletTypeSeeder extends Seeder
{
    /**
     * Run the database seeds.
     */
    public function run(): void
    {
        $walletTypes = [
            [
                'name' => 'Main Account',
                'slug' => 'main',
                'currency_code' => 'USD',
                'type' => 'fiat',
                'is_default' => true,
                'is_active' => true,
                'display_order' => 1,
            ],
            [
                'name' => 'Savings Account',
                'slug' => 'savings',
                'currency_code' => 'USD',
                'type' => 'fiat',
                'is_default' => true,
                'is_active' => true,
                'display_order' => 2,
            ],
            [
                'name' => 'Investment Account',
                'slug' => 'investment',
                'currency_code' => 'USD',
                'type' => 'fiat',
                'is_default' => false,
                'is_active' => true,
                'display_order' => 3,
            ],
            [
                'name' => 'Bitcoin Wallet',
                'slug' => 'btc',
                'currency_code' => 'BTC',
                'type' => 'crypto',
                'is_default' => false,
                'is_active' => true,
                'display_order' => 4,
            ],
            [
                'name' => 'Ethereum Wallet',
                'slug' => 'eth',
                'currency_code' => 'ETH',
                'type' => 'crypto',
                'is_default' => false,
                'is_active' => true,
                'display_order' => 5,
            ],
        ];

        foreach ($walletTypes as $walletType) {
            WalletType::updateOrCreate(
                ['slug' => $walletType['slug']],
                $walletType
            );
        }
    }
}
