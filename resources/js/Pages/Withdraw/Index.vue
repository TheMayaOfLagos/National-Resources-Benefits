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
    linkedAccountsCount: Number,
    bankWithdrawalEnabled: Boolean,
    expressWithdrawalEnabled: Boolean,
    todayWithdrawals: String,
    remainingDailyLimit: String,
    requiresVerification: Boolean,
});

// Check if bank withdrawal is available (has methods OR has linked accounts)
const hasBankWithdrawalOption = computed(() => {
    return props.manualMethodsCount > 0 || props.linkedAccountsCount > 0;
});

// Check if express withdrawal is available
const hasExpressWithdrawalOption = computed(() => {
    return props.automaticMethodsCount > 0;
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
            <p class="mt-1 text-gray-500 dark:text-gray-400">Request a withdrawal from your account</p>
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
                            class="flex items-center justify-center flex-shrink-0 w-12 h-12 bg-red-100 rounded-full dark:bg-red-900/30">
                            <i class="text-2xl text-red-600 pi pi-ban dark:text-red-400"></i>
                        </div>
                        <div class="flex-grow">
                            <h3 class="mb-2 text-lg font-semibold text-red-700 dark:text-red-400">Withdrawal Disabled
                            </h3>
                            <p class="mb-4 text-gray-600 dark:text-gray-400">{{ withdrawalMessage || 'Your withdrawal administrator.' }}</p>
                            <div class="p-4 rounded-lg bg-red-50 dark:bg-red-900/20">
                                <h4 class="mb-2 font-medium text-red-800 dark:text-red-300">What you can do:</h4>
                                <ul class="space-y-1 text-sm text-red-700 dark:text-red-400">
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
                            class="flex items-center justify-center flex-shrink-0 w-12 h-12 bg-red-100 rounded-full dark:bg-red-900/30">
                            <i class="text-2xl text-red-600 pi pi-ban dark:text-red-400"></i>
                        </div>
                        <div class="flex-grow">
                            <div class="flex items-center gap-3 mb-2">
                                <h3 class="text-lg font-semibold text-red-700 dark:text-red-400">Account Suspended</h3>
                                <Tag value="Suspended" severity="danger" />
                            </div>
                            <p class="mb-4 text-gray-600 dark:text-gray-400">
                                {{ withdrawalMessage || getDefaultStatusMessage('suspended') }}
                            </p>
                            <div class="p-4 rounded-lg bg-red-50 dark:bg-red-900/20">
                                <h4 class="mb-2 font-medium text-red-800 dark:text-red-300">Why was my account
                                    suspended?</h4>
                                <p class="mb-3 text-sm text-red-700 dark:text-red-400">
                                    Common reasons include: unusual activity patterns, policy violations,
                                    unverified identity documents, or pending security reviews.
                                </p>
                                <h4 class="mb-2 font-medium text-red-800 dark:text-red-300">Next Steps:</h4>
                                <ul class="space-y-1 text-sm text-red-700 dark:text-red-400">
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
                            <div class="flex gap-3 mt-4">
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
                            class="flex items-center justify-center flex-shrink-0 w-12 h-12 bg-yellow-100 rounded-full dark:bg-yellow-900/30">
                            <i class="text-2xl text-yellow-600 pi pi-clock dark:text-yellow-400"></i>
                        </div>
                        <div class="flex-grow">
                            <div class="flex items-center gap-3 mb-2">
                                <h3 class="text-lg font-semibold text-yellow-700 dark:text-yellow-400">Withdrawal On
                                    Hold</h3>
                                <Tag value="On Hold" severity="warn" />
                            </div>
                            <p class="mb-4 text-gray-600 dark:text-gray-400">
                                {{ withdrawalMessage || getDefaultStatusMessage('hold') }}
                            </p>
                            <div class="p-4 rounded-lg bg-yellow-50 dark:bg-yellow-900/20">
                                <h4 class="mb-2 font-medium text-yellow-800 dark:text-yellow-300">What does this mean?
                                </h4>
                                <p class="mb-3 text-sm text-yellow-700 dark:text-yellow-400">
                                    Your withdrawal capability has been temporarily placed on hold. This is often
                                    a precautionary measure and typically resolves within 24-72 hours.
                                </p>
                                <div class="flex items-center gap-2 text-sm text-yellow-700 dark:text-yellow-400">
                                    <i class="pi pi-info-circle"></i>
                                    <span>Your funds are safe and secure in your account.</span>
                                </div>
                            </div>
                            <div class="flex gap-3 mt-4">
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
                            class="flex items-center justify-center flex-shrink-0 w-12 h-12 bg-blue-100 rounded-full dark:bg-blue-900/30">
                            <i class="text-2xl text-blue-600 pi pi-search dark:text-blue-400"></i>
                        </div>
                        <div class="flex-grow">
                            <div class="flex items-center gap-3 mb-2">
                                <h3 class="text-lg font-semibold text-blue-700 dark:text-blue-400">Account Under Review
                                </h3>
                                <Tag value="Under Review" severity="info" />
                            </div>
                            <p class="mb-4 text-gray-600 dark:text-gray-400">
                                {{ withdrawalMessage || getDefaultStatusMessage('under_review') }}
                            </p>
                            <div class="p-4 rounded-lg bg-blue-50 dark:bg-blue-900/20">
                                <h4 class="mb-2 font-medium text-blue-800 dark:text-blue-300">Review Process</h4>
                                <div class="space-y-3">
                                    <div class="flex items-center gap-3">
                                        <div
                                            class="flex items-center justify-center w-8 h-8 text-sm font-bold text-blue-700 bg-blue-200 rounded-full dark:bg-blue-800 dark:text-blue-300">
                                            1</div>
                                        <span class="text-sm text-blue-700 dark:text-blue-400">Account flagged for
                                            routine review</span>
                                    </div>
                                    <div class="flex items-center gap-3">
                                        <div
                                            class="flex items-center justify-center w-8 h-8 text-sm font-bold text-blue-700 bg-blue-200 rounded-full dark:bg-blue-800 dark:text-blue-300">
                                            2</div>
                                        <span class="text-sm text-blue-700 dark:text-blue-400">Compliance team reviewing
                                            documentation</span>
                                    </div>
                                    <div class="flex items-center gap-3">
                                        <div
                                            class="flex items-center justify-center w-8 h-8 text-sm font-bold text-gray-500 bg-gray-200 rounded-full dark:bg-gray-700">
                                            3</div>
                                        <span class="text-sm text-gray-500">Review completion and status update</span>
                                    </div>
                                </div>
                                <p class="mt-3 text-sm text-blue-700 dark:text-blue-400">
                                    <i class="mr-1 pi pi-clock"></i>
                                    Estimated completion: 1-3 business days
                                </p>
                            </div>
                            <div class="flex gap-3 mt-4">
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
                <div class="grid grid-cols-2 gap-4 text-center md:grid-cols-4">
                    <div>
                        <p class="text-sm text-gray-500 dark:text-gray-400">Total Balance</p>
                        <p class="text-xl font-bold text-green-600">{{ settings.currency_symbol }}{{
                            totalBalance().toLocaleString('en-US', { minimumFractionDigits: 2 }) }}</p>
                    </div>
                    <div class="pl-4 border-l border-gray-200 dark:border-gray-700">
                        <p class="text-sm text-gray-500 dark:text-gray-400">Min Withdrawal</p>
                        <p class="text-xl font-bold text-primary-600">{{ settings.currency_symbol }}{{
                            settings.withdrawal_min }}</p>
                    </div>
                    <div class="pl-4 border-l border-gray-200 dark:border-gray-700">
                        <p class="text-sm text-gray-500 dark:text-gray-400">Max Withdrawal</p>
                        <p class="text-xl font-bold text-primary-600">{{ settings.currency_symbol }}{{
                            settings.withdrawal_max }}</p>
                    </div>
                    <div class="pl-4 border-l border-gray-200 dark:border-gray-700">
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
                <div v-if="accounts.length === 0" class="py-4 text-center text-gray-500">
                    <i class="mb-2 text-3xl pi pi-inbox"></i>
                    <p>No accounts found. Please contact support.</p>
                </div>
                <div v-else class="space-y-3">
                    <div v-for="account in accounts" :key="account.id"
                        class="flex items-center justify-between p-3 rounded-lg bg-gray-50 dark:bg-gray-800">
                        <div class="flex items-center gap-3">
                            <div
                                class="flex items-center justify-center w-10 h-10 rounded-full bg-primary-100 dark:bg-primary-900">
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
        <div v-if="bankWithdrawalEnabled || expressWithdrawalEnabled" class="grid gap-6" :class="[
            { 'opacity-50 pointer-events-none': !canWithdraw || withdrawalStatus !== 'approved' || requiresVerification },
            (bankWithdrawalEnabled && expressWithdrawalEnabled) ? 'md:grid-cols-2' : 'md:grid-cols-1 max-w-xl mx-auto'
        ]">
            <!-- Manual/Bank Withdrawal Card -->
            <Card v-if="bankWithdrawalEnabled" class="transition-shadow hover:shadow-lg">
                <template #header>
                    <div class="p-6 text-center text-white rounded-t-lg bg-gradient-to-r from-purple-500 to-purple-600">
                        <i class="mb-3 text-4xl pi pi-building"></i>
                        <h3 class="text-xl font-bold">Bank Withdrawal</h3>
                    </div>
                </template>
                <template #content>
                    <div class="text-center">
                        <p class="mb-4 text-gray-600 dark:text-gray-400">
                            Withdraw funds directly to your bank account via wire transfer or ACH.
                        </p>
                        <ul class="mb-4 space-y-2 text-sm text-left text-gray-500 dark:text-gray-400">
                            <li class="flex items-center gap-2">
                                <i class="text-green-500 pi pi-check"></i>
                                Bank Wire Transfer
                            </li>
                            <li class="flex items-center gap-2">
                                <i class="text-green-500 pi pi-check"></i>
                                ACH Direct Deposit
                            </li>
                            <li class="flex items-center gap-2">
                                <i class="text-green-500 pi pi-check"></i>
                                International Wire
                            </li>
                        </ul>
                        <p class="mb-4 text-xs text-gray-400">
                            Processing time: 1-5 business days
                        </p>
                        <!-- Show linked accounts info if user has them -->
                        <div v-if="linkedAccountsCount > 0"
                            class="p-2 mb-3 text-xs text-left text-green-700 rounded-lg bg-green-50 dark:bg-green-900/20 dark:text-green-400">
                            <i class="mr-1 pi pi-check-circle"></i>
                            You have {{ linkedAccountsCount }} linked bank account{{ linkedAccountsCount > 1 ? 's' : ''
                            }} ready for withdrawal
                        </div>
                        <Link :href="route('withdraw.manual')">
                        <Button label="Request Bank Withdrawal" icon="pi pi-arrow-right" iconPos="right" class="w-full"
                            :disabled="!hasBankWithdrawalOption" />
                        </Link>
                        <p v-if="!hasBankWithdrawalOption" class="mt-2 text-xs text-orange-500">
                            <Link :href="route('linked-accounts.index')" class="underline hover:text-orange-600">
                            Link a bank account
                            </Link>
                            to enable withdrawals
                        </p>
                    </div>
                </template>
            </Card>

            <!-- Automatic/Express Withdrawal Card -->
            <Card v-if="expressWithdrawalEnabled" class="transition-shadow hover:shadow-lg">
                <template #header>
                    <div class="p-6 text-center text-white rounded-t-lg bg-gradient-to-r from-indigo-500 to-indigo-600">
                        <i class="mb-3 text-4xl pi pi-bolt"></i>
                        <h3 class="text-xl font-bold">Express Withdrawal</h3>
                    </div>
                </template>
                <template #content>
                    <div class="text-center">
                        <p class="mb-4 text-gray-600 dark:text-gray-400">
                            Fast withdrawals via supported payment processors and digital wallets.
                        </p>
                        <ul class="mb-4 space-y-2 text-sm text-left text-gray-500 dark:text-gray-400">
                            <li class="flex items-center gap-2">
                                <i class="text-green-500 pi pi-check"></i>
                                PayPal Withdrawal
                            </li>
                            <li class="flex items-center gap-2">
                                <i class="text-green-500 pi pi-check"></i>
                                Crypto Wallet
                            </li>
                            <li class="flex items-center gap-2">
                                <i class="text-green-500 pi pi-check"></i>
                                E-Wallet Transfer
                            </li>
                        </ul>
                        <p class="mb-4 text-xs text-gray-400">
                            Processing time: 1-24 hours
                        </p>
                        <Link :href="route('withdraw.automatic')">
                        <Button label="Request Express Withdrawal" icon="pi pi-arrow-right" iconPos="right"
                            severity="help" class="w-full" :disabled="!hasExpressWithdrawalOption" />
                        </Link>
                        <p v-if="!hasExpressWithdrawalOption" class="mt-2 text-xs text-orange-500">
                            No express methods available
                        </p>
                    </div>
                </template>
            </Card>
        </div>

        <!-- No withdrawal methods available -->
        <Card v-if="!bankWithdrawalEnabled && !expressWithdrawalEnabled" class="text-center">
            <template #content>
                <div class="py-8">
                    <i class="mb-4 text-5xl text-gray-300 pi pi-ban dark:text-gray-600"></i>
                    <h3 class="mb-2 text-lg font-semibold text-gray-700 dark:text-gray-300">Withdrawals Unavailable</h3>
                    <p class="text-gray-500 dark:text-gray-400">
                        Withdrawal services are currently not available. Please contact support for assistance.
                    </p>
                    <Link :href="route('support.index')" class="mt-4">
                    <Button label="Contact Support" icon="pi pi-envelope" severity="secondary" outlined class="mt-4" />
                    </Link>
                </div>
            </template>
        </Card>

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
                <div class="h-3 mt-4 overflow-hidden bg-gray-200 rounded-full dark:bg-gray-700">
                    <div class="h-full transition-all duration-300 bg-primary-600"
                        :style="{ width: `${Math.min((parseFloat(todayWithdrawals.replace(/,/g, '')) / settings.withdrawal_limit_daily) * 100, 100)}%` }">
                    </div>
                </div>
            </template>
        </Card>

        <!-- Withdrawal History Link -->
        <div class="mt-8 text-center">
            <Link :href="route('withdraw.history')" class="font-medium text-primary-600 hover:text-primary-700">
            <i class="mr-2 pi pi-history"></i>
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
                <div class="grid gap-4 text-sm md:grid-cols-2">
                    <div>
                        <h4 class="mb-2 font-semibold text-gray-900 dark:text-white">Processing Times</h4>
                        <p class="text-gray-600 dark:text-gray-400">
                            Bank withdrawals typically take 1-5 business days. Express withdrawals are processed within
                            24 hours.
                        </p>
                    </div>
                    <div>
                        <h4 class="mb-2 font-semibold text-gray-900 dark:text-white">Verification Codes</h4>
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
