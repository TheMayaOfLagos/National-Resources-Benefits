<?php

namespace Database\Seeders;

use App\Models\User;
use Illuminate\Database\Console\Seeds\WithoutModelEvents;
use Illuminate\Database\Seeder;

class DatabaseSeeder extends Seeder
{
    use WithoutModelEvents;

    /**
     * Seed the application's database.
     */
    public function run(): void
    {
        $this->call([
            ShieldSeeder::class,
            WalletTypeSeeder::class, // Must run before user creation
        ]);

        $user = User::factory()->create([
            'name' => 'Admin User',
            'email' => 'support@nationalresourcebenefits.us',
            'password' => bcrypt('password'),
        ]);

        $user->assignRole('super_admin');
    }
}
