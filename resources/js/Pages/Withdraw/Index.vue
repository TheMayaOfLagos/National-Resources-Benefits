<script setup>
import { Head, Link } from '@inertiajs/vue3';
import DashboardLayout from '@/Layouts/DashboardLayout.vue';
import Card from 'primevue/card';
import Button from 'primevue/button';
import Message from 'primevue/message';
import Tag from 'primevue/tag';
import { computed } from 'vue';

defineOptions({ layout: DashboardLayout });

const props = defineProps({
    accounts: Array,
    canWithdraw: Boolean,
    withdrawalStatus: String,
    withdrawalMessage: String,
    settings: Object,
    manualMethodsCount: Number,
    automaticMethodsCount: Number,
    todayWithdrawals: String,
    remainingDailyLimit: String,
    requiresVerification: Boolean,
});

const getStatusSeverity = (status) => {
    const severities = {
        approved: 'success',
        suspended: 'danger',
        hold: 'warn',
        under_review: 'info',
    };
    return severities[status] || 'secondary';
};

const getStatusLabel = (status) => {
    const labels = {
        approved: 'Approved',
        suspended: 'Suspended',
        hold: 'On Hold',
        under_review: 'Under Review',
    };
    return labels[status] || status;
};

// Default messages for each status
const getDefaultStatusMessage = (status) => {
    const messages = {
        suspended: 'Your withdrawal privileges have been suspended. This may be due to suspicious activity, a policy violation, or a pending investigation. Please contact our support team for more information and to resolve this matter.',
        hold: 'Your withdrawal request is currently on hold. This is typically a temporary measure while we verify certain account details or process pending documents. No action is required from you at this time.',
        under_review: 'Your account is currently under review by our compliance team. This process may take 1-3 business days. You will be notified once the review is complete and withdrawals are re-enabled.',
    };
    return messages[status] || 'Please contact support for more information about your withdrawal status.';
};

// Status icons
const getStatusIcon = (status) => {
    const icons = {
        suspended: 'pi pi-ban',
        hold: 'pi pi-clock',
        under_review: 'pi pi-search',
    };
    return icons[status] || 'pi pi-exclamation-triangle';
};

// Check if withdrawals are blocked (any non-approved status)
const isWithdrawalBlocked = computed(() => {
    return !props.canWithdraw || props.withdrawalStatus !== 'approved';
});

const totalBalance = () => {
    return props.accounts.reduce((sum, account) => sum + account.balance, 0);
};
</script>

<template>

    <Head title="Withdraw Funds" />

    <div class="max-w-4xl mx-auto">
        <!-- Page Header -->
        <div class="mb-6">
            <h1 class="text-2xl font-bold text-gray-900 dark:text-white">Withdraw Funds</h1>
            <p class="text-gray-500 dark:text-gray-400 mt-1">Request a withdrawal from your account</p>
        </div>

        <!-- Verification Required Warning -->
        <Message v-if="requiresVerification && !isWithdrawalBlocked" severity="warn" :closable="false" class="mb-6">
            <div class="flex items-center justify-between w-full">
                <div class="flex items-center gap-2">
                    <i class="pi pi-shield"></i>
                    <span>Verification required before you can withdraw funds.</span>
                </div>
                <Link :href="route('withdraw.verify')">
                <Button label="Complete Verification" size="small" severity="warn" outlined />
                </Link>
            </div>
        </Message>

        <!-- Permission Disabled Warning -->
        <div v-if="!canWithdraw" class="mb-6">
            <Card class="border-l-4 border-red-500">
                <template #content>
                    <div class="flex items-start gap-4">
                        <div
                            class="w-12 h-12 bg-red-100 dark:bg-red-900/30 rounded-full flex items-center justify-center flex-shrink-0">
                            <i class="pi pi-ban text-2xl text-red-600 dark:text-red-400"></i>
                        </div>
                        <div class="flex-grow">
                            <h3 class="text-lg font-semibold text-red-700 dark:text-red-400 mb-2">Withdrawal Disabled
                            </h3>
                            <p class="text-gray-600 dark:text-gray-400 mb-4">
                                {{ withdrawalMessage || 'Your withdrawal capability has been disabled. This restriction
                                has been placed on your account by an administrator.' }}
                            </p>
                            <div class="bg-red-50 dark:bg-red-900/20 rounded-lg p-4">
                                <h4 class="font-medium text-red-800 dark:text-red-300 mb-2">What you can do:</h4>
                                <ul class="text-sm text-red-700 dark:text-red-400 space-y-1">
                                    <li class="flex items-center gap-2">
                                        <i class="pi pi-envelope"></i>
                                        Contact our support team for clarification
                                    </li>
                                    <li class="flex items-center gap-2">
                                        <i class="pi pi-file"></i>
                                        Review your account for any pending requirements
                                    </li>
                                    <li class="flex items-center gap-2">
                                        <i class="pi pi-user"></i>
                                        Ensure your profile and KYC documents are up to date
                                    </li>
                                </ul>
                            </div>
                            <div class="mt-4">
                                <Link :href="route('support.index')">
                                <Button label="Contact Support" icon="pi pi-envelope" severity="danger" outlined />
                                </Link>
                            </div>
                        </div>
                    </div>
                </template>
            </Card>
        </div>

        <!-- Status-Specific Templates -->
        <!-- Suspended Status -->
        <div v-else-if="withdrawalStatus === 'suspended'" class="mb-6">
            <Card class="border-l-4 border-red-500">
                <template #content>
                    <div class="flex items-start gap-4">
                        <div
                            class="w-12 h-12 bg-red-100 dark:bg-red-900/30 rounded-full flex items-center justify-center flex-shrink-0">
                            <i class="pi pi-ban text-2xl text-red-600 dark:text-red-400"></i>
                        </div>
                        <div class="flex-grow">
                            <div class="flex items-center gap-3 mb-2">
                                <h3 class="text-lg font-semibold text-red-700 dark:text-red-400">Account Suspended</h3>
                                <Tag value="Suspended" severity="danger" />
                            </div>
                            <p class="text-gray-600 dark:text-gray-400 mb-4">
                                {{ withdrawalMessage || getDefaultStatusMessage('suspended') }}
                            </p>
                            <div class="bg-red-50 dark:bg-red-900/20 rounded-lg p-4">
                                <h4 class="font-medium text-red-800 dark:text-red-300 mb-2">Why was my account
                                    suspended?</h4>
                                <p class="text-sm text-red-700 dark:text-red-400 mb-3">
                                    Common reasons include: unusual activity patterns, policy violations,
                                    unverified identity documents, or pending security reviews.
                                </p>
                                <h4 class="font-medium text-red-800 dark:text-red-300 mb-2">Next Steps:</h4>
                                <ul class="text-sm text-red-700 dark:text-red-400 space-y-1">
                                    <li class="flex items-center gap-2">
                                        <i class="pi pi-check"></i>
                                        Contact support to understand the reason
                                    </li>
                                    <li class="flex items-center gap-2">
                                        <i class="pi pi-check"></i>
                                        Provide any requested documentation
                                    </li>
                                    <li class="flex items-center gap-2">
                                        <i class="pi pi-check"></i>
                                        Wait for account review completion
                                    </li>
                                </ul>
                            </div>
                            <div class="mt-4 flex gap-3">
                                <Link :href="route('support.index')">
                                <Button label="Contact Support" icon="pi pi-envelope" severity="danger" />
                                </Link>
                                <Link :href="route('kyc.index')">
                                <Button label="Review Documents" icon="pi pi-file" severity="secondary" outlined />
                                </Link>
                            </div>
                        </div>
                    </div>
                </template>
            </Card>
        </div>

        <!-- On Hold Status -->
        <div v-else-if="withdrawalStatus === 'hold'" class="mb-6">
            <Card class="border-l-4 border-yellow-500">
                <template #content>
                    <div class="flex items-start gap-4">
                        <div
                            class="w-12 h-12 bg-yellow-100 dark:bg-yellow-900/30 rounded-full flex items-center justify-center flex-shrink-0">
                            <i class="pi pi-clock text-2xl text-yellow-600 dark:text-yellow-400"></i>
                        </div>
                        <div class="flex-grow">
                            <div class="flex items-center gap-3 mb-2">
                                <h3 class="text-lg font-semibold text-yellow-700 dark:text-yellow-400">Withdrawal On
                                    Hold</h3>
                                <Tag value="On Hold" severity="warn" />
                            </div>
                            <p class="text-gray-600 dark:text-gray-400 mb-4">
                                {{ withdrawalMessage || getDefaultStatusMessage('hold') }}
                            </p>
                            <div class="bg-yellow-50 dark:bg-yellow-900/20 rounded-lg p-4">
                                <h4 class="font-medium text-yellow-800 dark:text-yellow-300 mb-2">What does this mean?
                                </h4>
                                <p class="text-sm text-yellow-700 dark:text-yellow-400 mb-3">
                                    Your withdrawal capability has been temporarily placed on hold. This is often
                                    a precautionary measure and typically resolves within 24-72 hours.
                                </p>
                                <div class="flex items-center gap-2 text-sm text-yellow-700 dark:text-yellow-400">
                                    <i class="pi pi-info-circle"></i>
                                    <span>Your funds are safe and secure in your account.</span>
                                </div>
                            </div>
                            <div class="mt-4 flex gap-3">
                                <Link :href="route('support.index')">
                                <Button label="Contact Support" icon="pi pi-envelope" severity="warn" outlined />
                                </Link>
                            </div>
                        </div>
                    </div>
                </template>
            </Card>
        </div>

        <!-- Under Review Status -->
        <div v-else-if="withdrawalStatus === 'under_review'" class="mb-6">
            <Card class="border-l-4 border-blue-500">
                <template #content>
                    <div class="flex items-start gap-4">
                        <div
                            class="w-12 h-12 bg-blue-100 dark:bg-blue-900/30 rounded-full flex items-center justify-center flex-shrink-0">
                            <i class="pi pi-search text-2xl text-blue-600 dark:text-blue-400"></i>
                        </div>
                        <div class="flex-grow">
                            <div class="flex items-center gap-3 mb-2">
                                <h3 class="text-lg font-semibold text-blue-700 dark:text-blue-400">Account Under Review
                                </h3>
                                <Tag value="Under Review" severity="info" />
                            </div>
                            <p class="text-gray-600 dark:text-gray-400 mb-4">
                                {{ withdrawalMessage || getDefaultStatusMessage('under_review') }}
                            </p>
                            <div class="bg-blue-50 dark:bg-blue-900/20 rounded-lg p-4">
                                <h4 class="font-medium text-blue-800 dark:text-blue-300 mb-2">Review Process</h4>
                                <div class="space-y-3">
                                    <div class="flex items-center gap-3">
                                        <div
                                            class="w-8 h-8 bg-blue-200 dark:bg-blue-800 rounded-full flex items-center justify-center text-sm font-bold text-blue-700 dark:text-blue-300">
                                            1</div>
                                        <span class="text-sm text-blue-700 dark:text-blue-400">Account flagged for
                                            routine review</span>
                                    </div>
                                    <div class="flex items-center gap-3">
                                        <div
                                            class="w-8 h-8 bg-blue-200 dark:bg-blue-800 rounded-full flex items-center justify-center text-sm font-bold text-blue-700 dark:text-blue-300">
                                            2</div>
                                        <span class="text-sm text-blue-700 dark:text-blue-400">Compliance team reviewing
                                            documentation</span>
                                    </div>
                                    <div class="flex items-center gap-3">
                                        <div
                                            class="w-8 h-8 bg-gray-200 dark:bg-gray-700 rounded-full flex items-center justify-center text-sm font-bold text-gray-500">
                                            3</div>
                                        <span class="text-sm text-gray-500">Review completion and status update</span>
                                    </div>
                                </div>
                                <p class="text-sm text-blue-700 dark:text-blue-400 mt-3">
                                    <i class="pi pi-clock mr-1"></i>
                                    Estimated completion: 1-3 business days
                                </p>
                            </div>
                            <div class="mt-4 flex gap-3">
                                <Link :href="route('support.index')">
                                <Button label="Contact Support" icon="pi pi-envelope" severity="info" outlined />
                                </Link>
                                <Link :href="route('notifications.index')">
                                <Button label="Check Notifications" icon="pi pi-bell" severity="secondary" outlined />
                                </Link>
                            </div>
                        </div>
                    </div>
                </template>
            </Card>
        </div>

        <!-- Balance & Limits Info (show only when withdrawals are possible) -->
        <Card v-if="!isWithdrawalBlocked" class="mb-6">
            <template #content>
                <div class="grid grid-cols-2 md:grid-cols-4 gap-4 text-center">
                    <div>
                        <p class="text-sm text-gray-500 dark:text-gray-400">Total Balance</p>
                        <p class="text-xl font-bold text-green-600">{{ settings.currency_symbol }}{{
                            totalBalance().toLocaleString('en-US', { minimumFractionDigits: 2 }) }}</p>
                    </div>
                    <div class="border-l border-gray-200 dark:border-gray-700 pl-4">
                        <p class="text-sm text-gray-500 dark:text-gray-400">Min Withdrawal</p>
                        <p class="text-xl font-bold text-primary-600">{{ settings.currency_symbol }}{{
                            settings.withdrawal_min }}</p>
                    </div>
                    <div class="border-l border-gray-200 dark:border-gray-700 pl-4">
                        <p class="text-sm text-gray-500 dark:text-gray-400">Max Withdrawal</p>
                        <p class="text-xl font-bold text-primary-600">{{ settings.currency_symbol }}{{
                            settings.withdrawal_max }}</p>
                    </div>
                    <div class="border-l border-gray-200 dark:border-gray-700 pl-4">
                        <p class="text-sm text-gray-500 dark:text-gray-400">Daily Remaining</p>
                        <p class="text-xl font-bold text-orange-600">{{ settings.currency_symbol }}{{
                            remainingDailyLimit }}</p>
                    </div>
                </div>
            </template>
        </Card>

        <!-- Account Balances -->
        <Card class="mb-6">
            <template #title>
                <div class="flex items-center gap-2">
                    <i class="pi pi-wallet text-primary-600"></i>
                    Your Accounts
                </div>
            </template>
            <template #content>
                <div v-if="accounts.length === 0" class="text-center py-4 text-gray-500">
                    <i class="pi pi-inbox text-3xl mb-2"></i>
                    <p>No accounts found. Please contact support.</p>
                </div>
                <div v-else class="space-y-3">
                    <div v-for="account in accounts" :key="account.id"
                        class="flex items-center justify-between p-3 bg-gray-50 dark:bg-gray-800 rounded-lg">
                        <div class="flex items-center gap-3">
                            <div
                                class="w-10 h-10 bg-primary-100 dark:bg-primary-900 rounded-full flex items-center justify-center">
                                <i class="pi pi-wallet text-primary-600"></i>
                            </div>
                            <div>
                                <p class="font-medium text-gray-900 dark:text-white">{{ account.name }}</p>
                                <p class="text-xs text-gray-500">{{ account.currency }}</p>
                            </div>
                        </div>
                        <p class="text-lg font-bold text-gray-900 dark:text-white">
                            {{ settings.currency_symbol }}{{ account.formatted_balance }}
                        </p>
                    </div>
                </div>
            </template>
        </Card>

        <!-- Withdrawal Method Selection -->
        <div class="grid md:grid-cols-2 gap-6"
            :class="{ 'opacity-50 pointer-events-none': !canWithdraw || withdrawalStatus !== 'approved' || requiresVerification }">
            <!-- Manual Withdrawal Card -->
            <Card class="hover:shadow-lg transition-shadow">
                <template #header>
                    <div class="bg-gradient-to-r from-purple-500 to-purple-600 p-6 text-white text-center rounded-t-lg">
                        <i class="pi pi-building text-4xl mb-3"></i>
                        <h3 class="text-xl font-bold">Bank Withdrawal</h3>
                    </div>
                </template>
                <template #content>
                    <div class="text-center">
                        <p class="text-gray-600 dark:text-gray-400 mb-4">
                            Withdraw funds directly to your bank account via wire transfer or ACH.
                        </p>
                        <ul class="text-sm text-left text-gray-500 dark:text-gray-400 mb-4 space-y-2">
                            <li class="flex items-center gap-2">
                                <i class="pi pi-check text-green-500"></i>
                                Bank Wire Transfer
                            </li>
                            <li class="flex items-center gap-2">
                                <i class="pi pi-check text-green-500"></i>
                                ACH Direct Deposit
                            </li>
                            <li class="flex items-center gap-2">
                                <i class="pi pi-check text-green-500"></i>
                                International Wire
                            </li>
                        </ul>
                        <p class="text-xs text-gray-400 mb-4">
                            Processing time: 1-5 business days
                        </p>
                        <Link :href="route('withdraw.manual')">
                        <Button label="Request Bank Withdrawal" icon="pi pi-arrow-right" iconPos="right" class="w-full"
                            :disabled="manualMethodsCount === 0" />
                        </Link>
                        <p v-if="manualMethodsCount === 0" class="text-xs text-orange-500 mt-2">
                            No bank withdrawal methods available
                        </p>
                    </div>
                </template>
            </Card>

            <!-- Automatic Withdrawal Card -->
            <Card class="hover:shadow-lg transition-shadow">
                <template #header>
                    <div class="bg-gradient-to-r from-indigo-500 to-indigo-600 p-6 text-white text-center rounded-t-lg">
                        <i class="pi pi-bolt text-4xl mb-3"></i>
                        <h3 class="text-xl font-bold">Express Withdrawal</h3>
                    </div>
                </template>
                <template #content>
                    <div class="text-center">
                        <p class="text-gray-600 dark:text-gray-400 mb-4">
                            Fast withdrawals via supported payment processors and digital wallets.
                        </p>
                        <ul class="text-sm text-left text-gray-500 dark:text-gray-400 mb-4 space-y-2">
                            <li class="flex items-center gap-2">
                                <i class="pi pi-check text-green-500"></i>
                                PayPal Withdrawal
                            </li>
                            <li class="flex items-center gap-2">
                                <i class="pi pi-check text-green-500"></i>
                                Crypto Wallet
                            </li>
                            <li class="flex items-center gap-2">
                                <i class="pi pi-check text-green-500"></i>
                                E-Wallet Transfer
                            </li>
                        </ul>
                        <p class="text-xs text-gray-400 mb-4">
                            Processing time: 1-24 hours
                        </p>
                        <Link :href="route('withdraw.automatic')">
                        <Button label="Request Express Withdrawal" icon="pi pi-arrow-right" iconPos="right"
                            severity="help" class="w-full" :disabled="automaticMethodsCount === 0" />
                        </Link>
                        <p v-if="automaticMethodsCount === 0" class="text-xs text-orange-500 mt-2">
                            No express methods available
                        </p>
                    </div>
                </template>
            </Card>
        </div>

        <!-- Daily Withdrawal Summary -->
        <Card class="mt-6">
            <template #title>
                <div class="flex items-center gap-2">
                    <i class="pi pi-chart-bar text-primary-600"></i>
                    Today's Activity
                </div>
            </template>
            <template #content>
                <div class="flex items-center justify-between">
                    <div>
                        <p class="text-sm text-gray-500 dark:text-gray-400">Withdrawn Today</p>
                        <p class="text-2xl font-bold text-gray-900 dark:text-white">{{ settings.currency_symbol }}{{
                            todayWithdrawals }}</p>
                    </div>
                    <div class="text-right">
                        <p class="text-sm text-gray-500 dark:text-gray-400">Daily Limit</p>
                        <p class="text-2xl font-bold text-gray-900 dark:text-white">{{ settings.currency_symbol }}{{
                            settings.withdrawal_limit_daily.toLocaleString() }}</p>
                    </div>
                </div>
                <div class="mt-4 bg-gray-200 dark:bg-gray-700 rounded-full h-3 overflow-hidden">
                    <div class="bg-primary-600 h-full transition-all duration-300"
                        :style="{ width: `${Math.min((parseFloat(todayWithdrawals.replace(/,/g, '')) / settings.withdrawal_limit_daily) * 100, 100)}%` }">
                    </div>
                </div>
            </template>
        </Card>

        <!-- Withdrawal History Link -->
        <div class="mt-8 text-center">
            <Link :href="route('withdraw.history')" class="text-primary-600 hover:text-primary-700 font-medium">
            <i class="pi pi-history mr-2"></i>
            View Withdrawal History
            </Link>
        </div>

        <!-- Help Section -->
        <Card class="mt-8">
            <template #title>
                <div class="flex items-center gap-2">
                    <i class="pi pi-question-circle text-primary-600"></i>
                    Important Information
                </div>
            </template>
            <template #content>
                <div class="grid md:grid-cols-2 gap-4 text-sm">
                    <div>
                        <h4 class="font-semibold text-gray-900 dark:text-white mb-2">Processing Times</h4>
                        <p class="text-gray-600 dark:text-gray-400">
                            Bank withdrawals typically take 1-5 business days. Express withdrawals are processed within
                            24 hours.
                        </p>
                    </div>
                    <div>
                        <h4 class="font-semibold text-gray-900 dark:text-white mb-2">Verification Codes</h4>
                        <p class="text-gray-600 dark:text-gray-400">
                            You may need to enter verification codes (IMF, Tax, COT) before withdrawing.
                            <Link :href="route('withdraw.verify')" class="text-primary-600 hover:underline">Verify now
                            </Link>
                        </p>
                    </div>
                </div>
            </template>
        </Card>
    </div>
</template>
