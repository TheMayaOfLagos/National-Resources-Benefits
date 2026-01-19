<?php

namespace Database\Seeders;

use App\Models\WithdrawalFormField;
use Illuminate\Database\Seeder;

class WithdrawalFormFieldSeeder extends Seeder
{
    /**
     * Run the database seeds.
     */
    public function run(): void
    {
        $fields = [
            [
                'name' => 'bank_name',
                'label' => 'Bank Name',
                'type' => 'select',
                'placeholder' => 'Select your bank',
                'options' => [
                    ['value' => 'chase', 'label' => 'Chase Bank'],
                    ['value' => 'bank_of_america', 'label' => 'Bank of America'],
                    ['value' => 'wells_fargo', 'label' => 'Wells Fargo'],
                    ['value' => 'citibank', 'label' => 'Citibank'],
                    ['value' => 'us_bank', 'label' => 'U.S. Bank'],
                    ['value' => 'pnc', 'label' => 'PNC Bank'],
                    ['value' => 'capital_one', 'label' => 'Capital One'],
                    ['value' => 'td_bank', 'label' => 'TD Bank'],
                    ['value' => 'truist', 'label' => 'Truist Bank'],
                    ['value' => 'ally', 'label' => 'Ally Bank'],
                    ['value' => 'discover', 'label' => 'Discover Bank'],
                    ['value' => 'goldman_sachs', 'label' => 'Goldman Sachs (Marcus)'],
                    ['value' => 'american_express', 'label' => 'American Express Bank'],
                    ['value' => 'hsbc', 'label' => 'HSBC Bank USA'],
                    ['value' => 'santander', 'label' => 'Santander Bank'],
                    ['value' => 'bmo', 'label' => 'BMO Harris Bank'],
                    ['value' => 'fifth_third', 'label' => 'Fifth Third Bank'],
                    ['value' => 'regions', 'label' => 'Regions Bank'],
                    ['value' => 'huntington', 'label' => 'Huntington Bank'],
                    ['value' => 'navy_federal', 'label' => 'Navy Federal Credit Union'],
                    ['value' => 'usaa', 'label' => 'USAA'],
                    ['value' => 'chime', 'label' => 'Chime'],
                    ['value' => 'sofi', 'label' => 'SoFi'],
                    ['value' => 'other', 'label' => 'Other Bank'],
                ],
                'help_text' => 'Select the bank where you want to receive funds',
                'is_required' => true,
                'is_active' => true,
                'sort_order' => 1,
            ],
            [
                'name' => 'account_holder_name',
                'label' => 'Account Holder Name',
                'type' => 'text',
                'placeholder' => 'Enter the name on the bank account',
                'validation_rules' => ['min:2', 'max:100'],
                'help_text' => 'Enter the full name exactly as it appears on your bank account',
                'is_required' => true,
                'is_active' => true,
                'sort_order' => 2,
            ],
            [
                'name' => 'account_type',
                'label' => 'Account Type',
                'type' => 'select',
                'placeholder' => 'Select account type',
                'options' => [
                    ['value' => 'checking', 'label' => 'Checking Account'],
                    ['value' => 'savings', 'label' => 'Savings Account'],
                ],
                'help_text' => 'Select your bank account type',
                'is_required' => true,
                'is_active' => true,
                'sort_order' => 3,
            ],
            [
                'name' => 'routing_number',
                'label' => 'Routing Number (ABA)',
                'type' => 'text',
                'placeholder' => 'Enter 9-digit routing number',
                'validation_rules' => ['digits:9'],
                'help_text' => 'The 9-digit routing number found at the bottom of your checks',
                'is_required' => true,
                'is_active' => true,
                'sort_order' => 4,
            ],
            [
                'name' => 'account_number',
                'label' => 'Account Number',
                'type' => 'text',
                'placeholder' => 'Enter your bank account number',
                'validation_rules' => ['min:4', 'max:17'],
                'help_text' => 'Your bank account number (typically 10-12 digits)',
                'is_required' => true,
                'is_active' => true,
                'sort_order' => 5,
            ],
            [
                'name' => 'confirm_account_number',
                'label' => 'Confirm Account Number',
                'type' => 'text',
                'placeholder' => 'Re-enter your account number',
                'validation_rules' => ['same:account_data.account_number'],
                'help_text' => 'Please re-enter your account number to confirm',
                'is_required' => true,
                'is_active' => true,
                'sort_order' => 6,
            ],
            [
                'name' => 'bank_address',
                'label' => 'Bank Branch Address',
                'type' => 'textarea',
                'placeholder' => 'Enter the bank branch address (optional)',
                'validation_rules' => ['max:255'],
                'help_text' => 'The address of your bank branch (optional but may speed up processing)',
                'is_required' => false,
                'is_active' => false, // Disabled by default
                'sort_order' => 7,
            ],
            [
                'name' => 'swift_code',
                'label' => 'SWIFT/BIC Code',
                'type' => 'text',
                'placeholder' => 'Enter SWIFT code for international transfers',
                'validation_rules' => ['min:8', 'max:11'],
                'help_text' => 'Required only for international wire transfers (8-11 characters)',
                'is_required' => false,
                'is_active' => false, // Disabled by default, enable if international transfers are needed
                'sort_order' => 8,
            ],
        ];

        foreach ($fields as $field) {
            WithdrawalFormField::updateOrCreate(
                ['name' => $field['name']],
                $field
            );
        }

        $this->command->info('Withdrawal form fields seeded successfully!');
        $this->command->info('Total fields: ' . count($fields));
        $this->command->info('Active fields: ' . collect($fields)->where('is_active', true)->count());
    }
}
