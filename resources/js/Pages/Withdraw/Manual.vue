<script setup>
import { ref, computed, watch, onMounted } from 'vue';
import { Head, Link, useForm, usePage, router } from '@inertiajs/vue3';
import DashboardLayout from '@/Layouts/DashboardLayout.vue';
import Card from 'primevue/card';
import Button from 'primevue/button';
import Select from 'primevue/select';
import InputText from 'primevue/inputtext';
import InputNumber from 'primevue/inputnumber';
import Message from 'primevue/message';
import Steps from 'primevue/steps';
import Dialog from 'primevue/dialog';
import Divider from 'primevue/divider';
import Dropdown from 'primevue/dropdown';
import Textarea from 'primevue/textarea';
import Badge from 'primevue/badge';
import PasscodeDialog from '@/Components/PasscodeDialog.vue';

defineOptions({ layout: DashboardLayout });

const props = defineProps({
    accounts: Array,
    methods: Array,
    settings: Object,
    requiresVerification: Boolean,
    verificationStatus: Object,
    linkedAccounts: {
        type: Array,
        default: () => [],
    },
    withdrawalFormFields: {
        type: Array,
        default: () => [],
    },
    canAddMoreLinkedAccounts: {
        type: Boolean,
        default: true,
    },
    accountLimit: {
        type: Number,
        default: 3,
    },
    canWithdraw: {
        type: Boolean,
        default: true,
    },
    withdrawalStatus: {
        type: String,
        default: 'approved',
    },
    withdrawalMessage: {
        type: String,
        default: null,
    },
    passcodeStatus: {
        type: Object,
        default: () => ({
            has_passcode: false,
            requires_passcode: false,
            is_locked: false,
            lockout_remaining: 0,
        }),
    },
});

const page = usePage();
const flash = computed(() => page.props.flash || {});

const currentStep = ref(0);
const showConfirmDialog = ref(false);
const showPasscodeDialog = ref(false);
const showLinkAccountDialog = ref(false);
const useLinkedAccount = ref(true);
const passcodeVerified = ref(false);

// Dynamic steps based on whether admin methods are available
const steps = computed(() => {
    if (props.methods.length > 0) {
        return [
            { label: 'Account' },
            { label: 'Method' },
            { label: 'Amount' },
            { label: 'Bank Details' },
            { label: 'Confirm' },
        ];
    }
    // No admin methods - simplified flow using linked accounts
    return [
        { label: 'Account' },
        { label: 'Amount' },
        { label: 'Bank Details' },
        { label: 'Confirm' },
    ];
});

// Check if user can withdraw (has linked accounts or admin methods)
const canWithdrawMethods = computed(() => {
    return props.methods.length > 0 || props.linkedAccounts.length > 0;
});

// Check if withdrawals are blocked due to status or permission
const isWithdrawalBlocked = computed(() => {
    return !props.canWithdraw || props.withdrawalStatus !== 'approved';
});

// Default messages for each status
const getDefaultStatusMessage = (status) => {
    const messages = {
        suspended: 'Your withdrawal privileges have been suspended. Please contact support for assistance.',
        hold: 'Your withdrawal request is currently on hold. This is typically a temporary measure.',
        under_review: 'Your account is currently under review by our compliance team.',
    };
    return messages[status] || 'Please contact support for more information.';
};

// Step indices - dynamic based on flow
const hasAdminMethods = computed(() => props.methods.length > 0);
const stepIndices = computed(() => {
    if (hasAdminMethods.value) {
        return { account: 0, method: 1, amount: 2, bankDetails: 3, confirm: 4 };
    }
    return { account: 0, method: -1, amount: 1, bankDetails: 2, confirm: 3 };
});

const form = useForm({
    account_id: null,
    gateway_id: null,
    amount: null,
    linked_account_id: null,
    bank_details: {
        bank_name: '',
        account_name: '',
        account_number: '',
        routing_number: '',
        swift_code: '',
        iban: '',
        bank_address: '',
    },
});

// Form for adding new linked account
const linkAccountForm = useForm({
    account_name: '',
    account_data: {},
});

// Initialize linked account form fields
const initializeLinkAccountForm = () => {
    linkAccountForm.account_name = '';
    linkAccountForm.account_data = {};
    props.withdrawalFormFields.forEach(field => {
        linkAccountForm.account_data[field.name] = '';
    });
};

const openLinkAccountDialog = () => {
    initializeLinkAccountForm();
    showLinkAccountDialog.value = true;
};

const closeLinkAccountDialog = () => {
    showLinkAccountDialog.value = false;
    linkAccountForm.reset();
    linkAccountForm.clearErrors();
};

const submitLinkAccount = () => {
    linkAccountForm.post(route('linked-accounts.store'), {
        preserveScroll: true,
        onSuccess: () => {
            closeLinkAccountDialog();
            // Reload page to get updated linked accounts
            router.reload({ only: ['linkedAccounts', 'canAddMoreLinkedAccounts'] });
        },
    });
};

// Watch for linked account selection to populate bank details
watch(() => form.linked_account_id, (newId) => {
    if (newId) {
        const linkedAccount = props.linkedAccounts.find(a => a.id === newId);
        if (linkedAccount && linkedAccount.account_data) {
            form.bank_details = {
                ...form.bank_details,
                ...linkedAccount.account_data,
            };
        }
    }
});

const selectedAccount = computed(() => {
    return props.accounts.find(a => a.id === form.account_id);
});

const selectedMethod = computed(() => {
    return props.methods.find(m => m.id === form.gateway_id);
});

const selectedLinkedAccount = computed(() => {
    return props.linkedAccounts.find(a => a.id === form.linked_account_id);
});

const calculatedFee = computed(() => {
    if (!form.amount) return 0;
    // If using admin method, use its fees
    if (selectedMethod.value) {
        const feeFixed = selectedMethod.value.fee_fixed || 0;
        const feePercentage = selectedMethod.value.fee_percentage || 0;
        return feeFixed + (form.amount * feePercentage / 100);
    }
    // No fees for linked account direct withdrawals
    return 0;
});

const totalDeduction = computed(() => {
    return (form.amount || 0) + calculatedFee.value;
});

const netAmount = computed(() => {
    return form.amount || 0;
});

const minLimit = computed(() => {
    if (selectedMethod.value) {
        return Math.max(props.settings.withdrawal_min, selectedMethod.value.min_limit || 0);
    }
    return props.settings.withdrawal_min;
});

const maxLimit = computed(() => {
    if (selectedMethod.value) {
        const methodMax = selectedMethod.value.max_limit || Infinity;
        return Math.min(props.settings.withdrawal_max, methodMax);
    }
    return props.settings.withdrawal_max;
});

const maxWithdrawable = computed(() => {
    if (!selectedAccount.value) return 0;
    const balance = selectedAccount.value.balance;

    if (selectedMethod.value) {
        // Account for fee when calculating max
        const feeFixed = selectedMethod.value.fee_fixed || 0;
        const feePercentage = selectedMethod.value.fee_percentage || 0;
        const maxFromBalance = (balance - feeFixed) / (1 + feePercentage / 100);
        return Math.min(Math.max(0, maxFromBalance), maxLimit.value);
    }

    // No method fees for linked account withdrawals
    return Math.min(balance, maxLimit.value);
});

const canProceedStep1 = computed(() => form.account_id !== null);
const canProceedStep2 = computed(() => form.gateway_id !== null);

// Step validation for amount - works with or without admin methods
const canProceedAmount = computed(() => {
    return form.amount >= minLimit.value &&
        form.amount <= maxLimit.value &&
        totalDeduction.value <= (selectedAccount.value?.balance || 0);
});

// For backward compatibility when admin methods exist
const canProceedStep3 = computed(() => canProceedAmount.value);

const canProceedBankDetails = computed(() => {
    // If using linked account, just need to select one
    if (useLinkedAccount.value && props.linkedAccounts.length > 0) {
        return form.linked_account_id !== null;
    }
    // Manual entry requires bank details
    return form.bank_details.bank_name &&
        form.bank_details.account_name &&
        form.bank_details.account_number;
});

// For backward compatibility
const canProceedStep4 = computed(() => canProceedBankDetails.value);

const nextStep = () => {
    if (currentStep.value < steps.value.length - 1) {
        currentStep.value++;
    }
};

const prevStep = () => {
    if (currentStep.value > 0) {
        currentStep.value--;
    }
};

const goToStep = (index) => {
    // Only allow going back or to completed steps
    if (index <= currentStep.value) {
        currentStep.value = index;
    }
};

const setMaxAmount = () => {
    form.amount = Math.floor(maxWithdrawable.value * 100) / 100;
};

// Handle confirm button click - check if passcode is required
const handleConfirmClick = () => {
    if (props.passcodeStatus.requires_passcode) {
        showConfirmDialog.value = false;
        showPasscodeDialog.value = true;
    } else {
        submitWithdrawal();
    }
};

// Handle passcode verification success
const handlePasscodeVerified = (result) => {
    passcodeVerified.value = true;
    showPasscodeDialog.value = false;
    submitWithdrawal();
};

// Handle setup required - redirect to security settings
const handleSetupRequired = () => {
    router.visit(route('profile.security'));
};

const submitWithdrawal = () => {
    form.post(route('withdraw.store'), {
        preserveScroll: true,
        onSuccess: () => {
            showConfirmDialog.value = false;
            passcodeVerified.value = false;
            form.reset();
            currentStep.value = 0;
        },
        onError: (errors) => {
            showConfirmDialog.value = false;
            console.error('Withdrawal errors:', errors);
        },
    });
};
</script>

<template>

    <Head title="Bank Withdrawal" />

    <div class="max-w-3xl mx-auto">
        <!-- Page Header -->
        <div class="mb-6">
            <Link :href="route('withdraw.index')"
                class="inline-flex items-center mb-2 text-sm text-primary-600 hover:text-primary-700">
            <i class="mr-2 pi pi-arrow-left"></i>
            Back to Withdraw
            </Link>
            <h1 class="text-2xl font-bold text-gray-900 dark:text-white">Bank Withdrawal</h1>
            <p class="mt-1 text-gray-500 dark:text-gray-400">Request a withdrawal to your bank account</p>
        </div>

        <!-- Verification Required -->
        <Message v-if="requiresVerification && !isWithdrawalBlocked" severity="warn" :closable="false" class="mb-6">
            <div class="flex items-center justify-between w-full">
                <div class="flex items-center gap-2">
                    <i class="pi pi-shield"></i>
                    <span>Please complete verification before withdrawing.</span>
                </div>
                <Link :href="route('withdraw.verify')">
                <Button label="Verify Now" size="small" severity="warn" />
                </Link>
            </div>
        </Message>

        <!-- Withdrawal Blocked Status Templates -->
        <!-- Permission Disabled -->
        <Card v-if="!canWithdraw" class="mb-6 border-l-4 border-red-500">
            <template #content>
                <div class="flex items-start gap-4">
                    <div
                        class="flex items-center justify-center flex-shrink-0 w-12 h-12 bg-red-100 rounded-full dark:bg-red-900/30">
                        <i class="text-2xl text-red-600 pi pi-ban dark:text-red-400"></i>
                    </div>
                    <div class="flex-grow">
                        <h3 class="mb-2 text-lg font-semibold text-red-700 dark:text-red-400">Withdrawal Disabled</h3>
                        <p class="mb-4 text-gray-600 dark:text-gray-400">
                            {{ withdrawalMessage || 'Your withdrawal capability has been disabled. Please contact support.' }}
                        </p>
                        <Link :href="route('support.index')">
                        <Button label="Contact Support" icon="pi pi-envelope" severity="danger" outlined />
                        </Link>
                    </div>
                </div>
            </template>
        </Card>

        <!-- Suspended Status -->
        <Card v-else-if="withdrawalStatus === 'suspended'" class="mb-6 border-l-4 border-red-500">
            <template #content>
                <div class="flex items-start gap-4">
                    <div
                        class="flex items-center justify-center flex-shrink-0 w-12 h-12 bg-red-100 rounded-full dark:bg-red-900/30">
                        <i class="text-2xl text-red-600 pi pi-ban dark:text-red-400"></i>
                    </div>
                    <div class="flex-grow">
                        <h3 class="mb-2 text-lg font-semibold text-red-700 dark:text-red-400">Account Suspended</h3>
                        <p class="mb-4 text-gray-600 dark:text-gray-400">
                            {{ withdrawalMessage || getDefaultStatusMessage('suspended') }}
                        </p>
                        <Link :href="route('support.index')">
                        <Button label="Contact Support" icon="pi pi-envelope" severity="danger" />
                        </Link>
                    </div>
                </div>
            </template>
        </Card>

        <!-- On Hold Status -->
        <Card v-else-if="withdrawalStatus === 'hold'" class="mb-6 border-l-4 border-yellow-500">
            <template #content>
                <div class="flex items-start gap-4">
                    <div
                        class="flex items-center justify-center flex-shrink-0 w-12 h-12 bg-yellow-100 rounded-full dark:bg-yellow-900/30">
                        <i class="text-2xl text-yellow-600 pi pi-clock dark:text-yellow-400"></i>
                    </div>
                    <div class="flex-grow">
                        <h3 class="mb-2 text-lg font-semibold text-yellow-700 dark:text-yellow-400">Withdrawal On Hold
                        </h3>
                        <p class="mb-4 text-gray-600 dark:text-gray-400">
                            {{ withdrawalMessage || getDefaultStatusMessage('hold') }}
                        </p>
                        <Link :href="route('support.index')">
                        <Button label="Contact Support" icon="pi pi-envelope" severity="warn" outlined />
                        </Link>
                    </div>
                </div>
            </template>
        </Card>

        <!-- Under Review Status -->
        <Card v-else-if="withdrawalStatus === 'under_review'" class="mb-6 border-l-4 border-blue-500">
            <template #content>
                <div class="flex items-start gap-4">
                    <div
                        class="flex items-center justify-center flex-shrink-0 w-12 h-12 bg-blue-100 rounded-full dark:bg-blue-900/30">
                        <i class="text-2xl text-blue-600 pi pi-search dark:text-blue-400"></i>
                    </div>
                    <div class="flex-grow">
                        <h3 class="mb-2 text-lg font-semibold text-blue-700 dark:text-blue-400">Account Under Review
                        </h3>
                        <p class="mb-4 text-gray-600 dark:text-gray-400">
                            {{ withdrawalMessage || getDefaultStatusMessage('under_review') }}
                        </p>
                        <div class="flex gap-3">
                            <Link :href="route('support.index')">
                            <Button label="Contact Support" icon="pi pi-envelope" severity="info" outlined />
                            </Link>
                        </div>
                    </div>
                </div>
            </template>
        </Card>

        <!-- Flash Messages -->
        <Message v-if="flash.success" severity="success" :closable="true" class="mb-6">
            {{ flash.success }}
        </Message>
        <Message v-if="flash.error" severity="error" :closable="true" class="mb-6">
            {{ flash.error }}
        </Message>

        <!-- Form Errors -->
        <Message v-if="Object.keys(form.errors).length > 0" severity="error" :closable="true" class="mb-6">
            <ul class="list-disc list-inside">
                <li v-for="(error, field) in form.errors" :key="field">{{ error }}</li>
            </ul>
        </Message>

        <!-- No Withdrawal Options Available (only show if not blocked) -->
        <Message v-if="!isWithdrawalBlocked && !canWithdrawMethods" severity="info" :closable="false" class="mb-6">
            <div class="flex flex-col gap-4">
                <div class="flex items-center gap-2">
                    <i class="pi pi-info-circle"></i>
                    <span>No withdrawal methods available. Please link a bank account to withdraw funds.</span>
                </div>
                <div class="flex items-center gap-3">
                    <Link :href="route('linked-accounts.index')">
                    <Button label="Link Bank Account" icon="pi pi-plus" size="small" />
                    </Link>
                </div>
            </div>
        </Message>

        <!-- Wizard Content - Show if user has linked accounts OR admin methods AND not blocked -->
        <div v-if="!isWithdrawalBlocked && canWithdrawMethods && !requiresVerification">
            <!-- Steps Indicator -->
            <Card class="mb-6">
                <template #content>
                    <Steps :model="steps" :activeStep="currentStep" :readonly="false"
                        @step-change="(e) => goToStep(e.index)" />
                </template>
            </Card>

            <!-- Step 1: Select Account -->
            <Card v-if="currentStep === stepIndices.account" class="mb-6">
                <template #title>
                    <div class="flex items-center gap-2">
                        <i class="pi pi-wallet text-primary-600"></i>
                        Select Source Account
                    </div>
                </template>
                <template #content>
                    <div class="space-y-3">
                        <div v-for="account in accounts" :key="account.id"
                            class="p-4 transition-all duration-300 ease-in-out border-2 rounded-lg cursor-pointer"
                            :class="form.account_id === account.id
                                ? 'border-green-500 bg-green-50 dark:bg-green-900/20 shadow-md shadow-green-500/20'
                                : 'border-gray-200 dark:border-gray-700 hover:border-gray-300 dark:hover:border-gray-600'"
                            @click="form.account_id = account.id">
                            <div class="flex items-center justify-between">
                                <div class="flex items-center gap-3">
                                    <div class="flex items-center justify-center w-10 h-10 transition-all duration-300 rounded-full"
                                        :class="form.account_id === account.id ? 'bg-green-500 text-white' : 'bg-gray-100 dark:bg-gray-700 text-gray-500'">
                                        <i :class="form.account_id === account.id ? 'pi pi-check' : 'pi pi-wallet'"
                                            class="transition-all duration-300"></i>
                                    </div>
                                    <div>
                                        <p class="font-medium text-gray-900 dark:text-white">{{ account.name }}</p>
                                        <p class="text-sm text-gray-500 dark:text-gray-400">{{ account.currency }}</p>
                                    </div>
                                </div>
                                <div class="text-right">
                                    <p class="text-lg font-bold"
                                        :class="form.account_id === account.id ? 'text-green-600' : 'text-gray-900 dark:text-white'">
                                        {{ settings.currency_symbol }}{{ account.formatted_balance }}
                                    </p>
                                    <p class="text-xs text-gray-500">Available</p>
                                </div>
                            </div>
                        </div>
                    </div>

                    <div class="flex justify-end mt-6">
                        <Button label="Continue" icon="pi pi-arrow-right" iconPos="right" :disabled="!canProceedStep1"
                            @click="nextStep" />
                    </div>
                </template>
            </Card>

            <!-- Step 2: Select Method (only shown when admin methods exist) -->
            <Card v-if="hasAdminMethods && currentStep === stepIndices.method" class="mb-6">
                <template #title>
                    <div class="flex items-center gap-2">
                        <i class="pi pi-credit-card text-primary-600"></i>
                        Select Withdrawal Method
                    </div>
                </template>
                <template #content>
                    <div class="space-y-3">
                        <div v-for="method in methods" :key="method.id"
                            class="p-4 transition-all duration-300 ease-in-out border-2 rounded-lg cursor-pointer"
                            :class="form.gateway_id === method.id
                                ? 'border-green-500 bg-green-50 dark:bg-green-900/20 shadow-md shadow-green-500/20'
                                : 'border-gray-200 dark:border-gray-700 hover:border-gray-300 dark:hover:border-gray-600'"
                            @click="form.gateway_id = method.id">
                            <div class="flex items-center justify-between">
                                <div class="flex items-center gap-3">
                                    <div class="flex items-center justify-center w-12 h-12 transition-all duration-300 rounded-lg"
                                        :class="form.gateway_id === method.id ? 'bg-green-500' : 'bg-gray-100 dark:bg-gray-700'">
                                        <i v-if="form.gateway_id === method.id"
                                            class="text-xl text-white pi pi-check"></i>
                                        <img v-else-if="method.logo" :src="method.logo" :alt="method.name"
                                            class="object-contain w-8 h-8" />
                                        <i v-else class="text-xl text-gray-500 pi pi-building"></i>
                                    </div>
                                    <div>
                                        <p class="font-medium text-gray-900 dark:text-white">{{ method.name }}</p>
                                        <p class="text-xs text-gray-500 dark:text-gray-400">{{ method.processing_time }}
                                        </p>
                                    </div>
                                </div>
                                <div class="text-sm text-right">
                                    <p v-if="method.fee_fixed > 0 || method.fee_percentage > 0" class="text-orange-600">
                                        Fee: {{ settings.currency_symbol }}{{ method.fee_fixed }}
                                        <span v-if="method.fee_percentage > 0"> + {{ method.fee_percentage }}%</span>
                                    </p>
                                    <p v-else class="text-green-600">No fees</p>
                                    <p class="mt-1 text-xs text-gray-500">
                                        Limit: {{ settings.currency_symbol }}{{ method.min_limit }} - {{
                                            settings.currency_symbol }}{{ method.max_limit || 'No limit' }}
                                    </p>
                                </div>
                            </div>
                        </div>
                    </div>

                    <div class="flex justify-between mt-6">
                        <Button label="Back" icon="pi pi-arrow-left" severity="secondary" outlined @click="prevStep" />
                        <Button label="Continue" icon="pi pi-arrow-right" iconPos="right" :disabled="!canProceedStep2"
                            @click="nextStep" />
                    </div>
                </template>
            </Card>

            <!-- Step 3: Enter Amount -->
            <Card v-if="currentStep === stepIndices.amount" class="mb-6">
                <template #title>
                    <div class="flex items-center gap-2">
                        <i class="pi pi-dollar text-primary-600"></i>
                        Enter Amount
                    </div>
                </template>
                <template #content>
                    <div class="p-4 mb-6 rounded-lg bg-gray-50 dark:bg-gray-800">
                        <div class="flex justify-between mb-2 text-sm">
                            <span class="text-gray-500">Available Balance:</span>
                            <span class="font-medium">{{ settings.currency_symbol }}{{
                                selectedAccount?.formatted_balance }}</span>
                        </div>
                        <div class="flex justify-between mb-2 text-sm">
                            <span class="text-gray-500">Minimum Withdrawal:</span>
                            <span class="font-medium">{{ settings.currency_symbol }}{{ minLimit.toLocaleString()
                            }}</span>
                        </div>
                        <div class="flex justify-between text-sm">
                            <span class="text-gray-500">Maximum Withdrawal:</span>
                            <span class="font-medium">{{ settings.currency_symbol }}{{ maxLimit.toLocaleString()
                            }}</span>
                        </div>
                    </div>

                    <div class="mb-6">
                        <label class="block mb-2 text-sm font-medium text-gray-700 dark:text-gray-300">
                            Withdrawal Amount
                        </label>
                        <div class="flex gap-2">
                            <InputNumber v-model="form.amount" :min="minLimit" :max="maxLimit" mode="currency"
                                currency="USD" locale="en-US" class="flex-1" placeholder="Enter amount" />
                            <Button label="Max" severity="secondary" outlined @click="setMaxAmount" />
                        </div>
                        <p v-if="form.errors.amount" class="mt-1 text-sm text-red-500">{{ form.errors.amount }}</p>
                    </div>

                    <!-- Fee Breakdown -->
                    <div class="p-4 mb-6 rounded-lg bg-blue-50 dark:bg-blue-900/20">
                        <h4 class="mb-3 font-medium text-gray-900 dark:text-white">Transaction Summary</h4>
                        <div class="space-y-2 text-sm">
                            <div class="flex justify-between">
                                <span class="text-gray-600 dark:text-gray-400">Withdrawal Amount:</span>
                                <span class="font-medium">{{ settings.currency_symbol }}{{ (form.amount ||
                                    0).toLocaleString('en-US', { minimumFractionDigits: 2 }) }}</span>
                            </div>
                            <div class="flex justify-between">
                                <span class="text-gray-600 dark:text-gray-400">Processing Fee:</span>
                                <span class="font-medium text-orange-600">-{{ settings.currency_symbol }}{{
                                    calculatedFee.toLocaleString('en-US', { minimumFractionDigits: 2 }) }}</span>
                            </div>
                            <Divider />
                            <div class="flex justify-between text-base">
                                <span class="font-semibold text-gray-900 dark:text-white">Total Deduction:</span>
                                <span class="font-bold text-primary-600">{{ settings.currency_symbol }}{{
                                    totalDeduction.toLocaleString('en-US', { minimumFractionDigits: 2 }) }}</span>
                            </div>
                            <div class="flex justify-between text-base">
                                <span class="font-semibold text-gray-900 dark:text-white">You'll Receive:</span>
                                <span class="font-bold text-green-600">{{ settings.currency_symbol }}{{
                                    netAmount.toLocaleString('en-US', { minimumFractionDigits: 2 }) }}</span>
                            </div>
                        </div>
                    </div>

                    <Message v-if="totalDeduction > (selectedAccount?.balance || 0)" severity="error" :closable="false">
                        Insufficient balance. Total deduction exceeds available balance.
                    </Message>

                    <div class="flex justify-between mt-6">
                        <Button label="Back" icon="pi pi-arrow-left" severity="secondary" outlined @click="prevStep" />
                        <Button label="Continue" icon="pi pi-arrow-right" iconPos="right" :disabled="!canProceedStep3"
                            @click="nextStep" />
                    </div>
                </template>
            </Card>

            <!-- Step 4: Bank Details -->
            <Card v-if="currentStep === stepIndices.bankDetails" class="mb-6">
                <template #title>
                    <div class="flex items-center gap-2">
                        <i class="pi pi-building text-primary-600"></i>
                        Bank Account Details
                    </div>
                </template>
                <template #subtitle>
                    Select a linked account or enter new bank details
                </template>
                <template #content>
                    <!-- Linked Accounts Selection -->
                    <div v-if="linkedAccounts.length > 0" class="mb-6">
                        <div class="flex items-center justify-between mb-3">
                            <label class="text-sm font-medium text-gray-700 dark:text-gray-300">
                                Use Linked Account
                            </label>
                            <Button v-if="canAddMoreLinkedAccounts" label="Link New Account" icon="pi pi-plus"
                                size="small" severity="secondary" outlined @click="openLinkAccountDialog" />
                        </div>

                        <div class="mb-4 space-y-2">
                            <div v-for="account in linkedAccounts" :key="account.id"
                                class="p-3 transition-all border-2 rounded-lg cursor-pointer" :class="form.linked_account_id === account.id
                                    ? 'border-green-500 bg-green-50 dark:bg-green-900/20'
                                    : 'border-gray-200 dark:border-gray-700 hover:border-gray-300'"
                                @click="form.linked_account_id = account.id; useLinkedAccount = true">
                                <div class="flex items-center justify-between">
                                    <div class="flex items-center gap-3">
                                        <div class="flex items-center justify-center w-8 h-8 rounded-full"
                                            :class="form.linked_account_id === account.id ? 'bg-green-500 text-white' : 'bg-gray-100 dark:bg-gray-700'">
                                            <i :class="form.linked_account_id === account.id ? 'pi pi-check' : 'pi pi-building'"
                                                class="text-sm"></i>
                                        </div>
                                        <div>
                                            <p class="text-sm font-medium text-gray-900 dark:text-white">{{
                                                account.account_name }}</p>
                                            <p class="text-xs text-gray-500">{{ account.display_name }}</p>
                                        </div>
                                    </div>
                                    <div class="flex items-center gap-2">
                                        <Badge v-if="account.is_default" value="Default" severity="success" />
                                        <Badge :value="account.is_verified ? 'Verified' : 'Pending'"
                                            :severity="account.is_verified ? 'info' : 'warn'" />
                                    </div>
                                </div>
                            </div>
                        </div>

                        <div class="mb-4 text-center">
                            <Button :label="useLinkedAccount ? 'Enter Bank Details Manually' : 'Use Linked Account'"
                                severity="secondary" text size="small"
                                @click="useLinkedAccount = !useLinkedAccount; if (useLinkedAccount) form.linked_account_id = linkedAccounts[0]?.id" />
                        </div>
                    </div>

                    <!-- No Linked Accounts Message -->
                    <div v-else class="p-4 mb-6 rounded-lg bg-blue-50 dark:bg-blue-900/20">
                        <div class="flex items-start gap-3">
                            <i class="pi pi-info-circle text-blue-500 mt-0.5"></i>
                            <div class="flex-1">
                                <p class="mb-2 text-sm text-gray-700 dark:text-gray-300">
                                    You don't have any linked withdrawal accounts yet. Link an account to save your bank
                                    details for faster withdrawals.
                                </p>
                                <Button v-if="canAddMoreLinkedAccounts" label="Link Bank Account" icon="pi pi-plus"
                                    size="small" @click="openLinkAccountDialog" />
                            </div>
                        </div>
                    </div>

                    <!-- Manual Entry Form (shown when not using linked account or no linked accounts) -->
                    <div v-if="!useLinkedAccount || linkedAccounts.length === 0" class="grid gap-4 md:grid-cols-2">
                        <div class="md:col-span-2">
                            <label class="block mb-2 text-sm font-medium text-gray-700 dark:text-gray-300">
                                Bank Name <span class="text-red-500">*</span>
                            </label>
                            <InputText v-model="form.bank_details.bank_name" class="w-full"
                                placeholder="e.g., Chase Bank" />
                        </div>

                        <div class="md:col-span-2">
                            <label class="block mb-2 text-sm font-medium text-gray-700 dark:text-gray-300">
                                Account Holder Name <span class="text-red-500">*</span>
                            </label>
                            <InputText v-model="form.bank_details.account_name" class="w-full"
                                placeholder="Name as it appears on account" />
                        </div>

                        <div>
                            <label class="block mb-2 text-sm font-medium text-gray-700 dark:text-gray-300">
                                Account Number <span class="text-red-500">*</span>
                            </label>
                            <InputText v-model="form.bank_details.account_number" class="w-full"
                                placeholder="Account number" />
                        </div>

                        <div>
                            <label class="block mb-2 text-sm font-medium text-gray-700 dark:text-gray-300">
                                Routing Number
                            </label>
                            <InputText v-model="form.bank_details.routing_number" class="w-full"
                                placeholder="9-digit routing number" maxlength="9" />
                        </div>

                        <div>
                            <label class="block mb-2 text-sm font-medium text-gray-700 dark:text-gray-300">
                                SWIFT/BIC Code
                            </label>
                            <InputText v-model="form.bank_details.swift_code" class="w-full"
                                placeholder="For international transfers" />
                        </div>

                        <div>
                            <label class="block mb-2 text-sm font-medium text-gray-700 dark:text-gray-300">
                                IBAN
                            </label>
                            <InputText v-model="form.bank_details.iban" class="w-full"
                                placeholder="International Bank Account Number" />
                        </div>

                        <div class="md:col-span-2">
                            <label class="block mb-2 text-sm font-medium text-gray-700 dark:text-gray-300">
                                Bank Address
                            </label>
                            <InputText v-model="form.bank_details.bank_address" class="w-full"
                                placeholder="Bank branch address (optional)" />
                        </div>
                    </div>

                    <div class="flex justify-between mt-6">
                        <Button label="Back" icon="pi pi-arrow-left" severity="secondary" outlined @click="prevStep" />
                        <Button label="Review Withdrawal" icon="pi pi-arrow-right" iconPos="right"
                            :disabled="!canProceedStep4" @click="nextStep" />
                    </div>
                </template>
            </Card>

            <!-- Step 5: Confirmation -->
            <Card v-if="currentStep === stepIndices.confirm" class="mb-6">
                <template #title>
                    <div class="flex items-center gap-2">
                        <i class="pi pi-check-circle text-primary-600"></i>
                        Review & Confirm
                    </div>
                </template>
                <template #content>
                    <div class="space-y-4">
                        <!-- Account Info -->
                        <div class="p-4 rounded-lg bg-gray-50 dark:bg-gray-800">
                            <h4 class="mb-2 text-sm font-medium text-gray-500 dark:text-gray-400">Source Account</h4>
                            <p class="font-semibold text-gray-900 dark:text-white">{{ selectedAccount?.name }}</p>
                            <p class="text-sm text-gray-500">Balance: {{ settings.currency_symbol }}{{
                                selectedAccount?.formatted_balance }}</p>
                        </div>

                        <!-- Method Info (Admin Method) -->
                        <div v-if="selectedMethod" class="p-4 rounded-lg bg-gray-50 dark:bg-gray-800">
                            <h4 class="mb-2 text-sm font-medium text-gray-500 dark:text-gray-400">Withdrawal Method</h4>
                            <p class="font-semibold text-gray-900 dark:text-white">{{ selectedMethod?.name }}</p>
                            <p class="text-sm text-gray-500">{{ selectedMethod?.processing_time }}</p>
                        </div>

                        <!-- Linked Account Info -->
                        <div v-else-if="selectedLinkedAccount" class="p-4 rounded-lg bg-gray-50 dark:bg-gray-800">
                            <h4 class="mb-2 text-sm font-medium text-gray-500 dark:text-gray-400">Withdrawal Method</h4>
                            <p class="font-semibold text-gray-900 dark:text-white">Bank Transfer (Linked Account)</p>
                            <p class="text-sm text-gray-500">1-3 business days</p>
                        </div>

                        <!-- Bank Details (from linked account or manual entry) -->
                        <div class="p-4 rounded-lg bg-gray-50 dark:bg-gray-800">
                            <h4 class="mb-2 text-sm font-medium text-gray-500 dark:text-gray-400">Bank Details</h4>
                            <div v-if="useLinkedAccount && selectedLinkedAccount" class="space-y-1 text-sm">
                                <p class="font-medium text-gray-900 dark:text-white">{{
                                    selectedLinkedAccount.account_name }}</p>
                                <p class="text-gray-500">{{ selectedLinkedAccount.display_name }}</p>
                            </div>
                            <div v-else class="grid grid-cols-2 gap-2 text-sm">
                                <p class="text-gray-500">Bank:</p>
                                <p class="font-medium text-gray-900 dark:text-white">{{ form.bank_details.bank_name }}
                                </p>
                                <p class="text-gray-500">Account Name:</p>
                                <p class="font-medium text-gray-900 dark:text-white">{{ form.bank_details.account_name
                                    }}</p>
                                <p class="text-gray-500">Account Number:</p>
                                <p class="font-medium text-gray-900 dark:text-white">****{{
                                    form.bank_details.account_number?.slice(-4) }}</p>
                                <p v-if="form.bank_details.routing_number" class="text-gray-500">Routing:</p>
                                <p v-if="form.bank_details.routing_number"
                                    class="font-medium text-gray-900 dark:text-white">{{
                                    form.bank_details.routing_number }}</p>
                            </div>
                        </div>

                        <!-- Amount Summary -->
                        <div class="p-4 rounded-lg bg-green-50 dark:bg-green-900/20">
                            <h4 class="mb-3 text-sm font-medium text-gray-500 dark:text-gray-400">Transaction Summary
                            </h4>
                            <div class="space-y-2">
                                <div class="flex justify-between">
                                    <span class="text-gray-600 dark:text-gray-400">Withdrawal Amount:</span>
                                    <span class="font-medium">{{ settings.currency_symbol }}{{
                                        form.amount.toLocaleString('en-US', { minimumFractionDigits: 2 }) }}</span>
                                </div>
                                <div class="flex justify-between">
                                    <span class="text-gray-600 dark:text-gray-400">Fee:</span>
                                    <span class="font-medium text-orange-600">-{{ settings.currency_symbol }}{{
                                        calculatedFee.toLocaleString('en-US', { minimumFractionDigits: 2 }) }}</span>
                                </div>
                                <Divider />
                                <div class="flex justify-between text-lg">
                                    <span class="font-bold text-gray-900 dark:text-white">You'll Receive:</span>
                                    <span class="font-bold text-green-600">{{ settings.currency_symbol }}{{
                                        netAmount.toLocaleString('en-US', { minimumFractionDigits: 2 }) }}</span>
                                </div>
                            </div>
                        </div>
                    </div>

                    <Message severity="info" :closable="false" class="mt-4">
                        <i class="mr-2 pi pi-info-circle"></i>
                        By proceeding, you agree that the funds will be deducted from your account and transferred to
                        the provided bank details.
                    </Message>

                    <div class="flex justify-between mt-6">
                        <Button label="Back" icon="pi pi-arrow-left" severity="secondary" outlined @click="prevStep" />
                        <Button label="Submit Withdrawal" icon="pi pi-check" severity="success"
                            :loading="form.processing" @click="showConfirmDialog = true" />
                    </div>
                </template>
            </Card>
        </div>

        <!-- Confirmation Dialog -->
        <Dialog v-model:visible="showConfirmDialog" modal header="Confirm Withdrawal" :style="{ width: '450px' }">
            <div class="text-center">
                <i class="mb-4 text-5xl text-yellow-500 pi pi-exclamation-triangle"></i>
                <p class="mb-4 text-gray-600 dark:text-gray-400">
                    Are you sure you want to withdraw
                    <strong class="text-gray-900 dark:text-white">{{ settings.currency_symbol }}{{
                        form.amount?.toLocaleString('en-US', { minimumFractionDigits: 2 }) }}</strong>?
                </p>
                <p class="text-sm text-gray-500">
                    This action cannot be undone. The funds will be sent to your bank account.
                </p>
            </div>
            <template #footer>
                <Button label="Cancel" severity="secondary" outlined @click="showConfirmDialog = false" />
                <Button label="Confirm Withdrawal" severity="success" :loading="form.processing"
                    @click="handleConfirmClick" />
            </template>
        </Dialog>

        <!-- Passcode Verification Dialog -->
        <PasscodeDialog :show="showPasscodeDialog" :passcode-status="passcodeStatus" @close="showPasscodeDialog = false"
            @verified="handlePasscodeVerified" @setup-required="handleSetupRequired" />

        <!-- Link Account Dialog -->
        <Dialog v-model:visible="showLinkAccountDialog" modal header="Link Withdrawal Account"
            :style="{ width: '500px' }" :closable="!linkAccountForm.processing">
            <form @submit.prevent="submitLinkAccount" class="space-y-4">
                <!-- Account Nickname -->
                <div>
                    <label class="block mb-1 text-sm font-medium text-gray-700 dark:text-gray-300">
                        Account Nickname *
                    </label>
                    <InputText v-model="linkAccountForm.account_name" class="w-full"
                        placeholder="e.g., My Chase Account"
                        :class="{ 'p-invalid': linkAccountForm.errors.account_name }" />
                    <small class="text-gray-500">A friendly name to identify this account</small>
                    <small v-if="linkAccountForm.errors.account_name" class="block mt-1 p-error">
                        {{ linkAccountForm.errors.account_name }}
                    </small>
                </div>

                <!-- Dynamic Form Fields -->
                <div v-for="field in withdrawalFormFields" :key="field.id">
                    <label class="block mb-1 text-sm font-medium text-gray-700 dark:text-gray-300">
                        {{ field.label }} {{ field.is_required ? '*' : '' }}
                    </label>

                    <!-- Text Input -->
                    <InputText v-if="field.type === 'text' || field.type === 'email' || field.type === 'tel'"
                        v-model="linkAccountForm.account_data[field.name]" class="w-full"
                        :placeholder="field.placeholder || ''" :type="field.type"
                        :class="{ 'p-invalid': linkAccountForm.errors[`account_data.${field.name}`] }" />

                    <!-- Number Input -->
                    <InputText v-else-if="field.type === 'number'" v-model="linkAccountForm.account_data[field.name]"
                        class="w-full" :placeholder="field.placeholder || ''" type="number"
                        :class="{ 'p-invalid': linkAccountForm.errors[`account_data.${field.name}`] }" />

                    <!-- Select Dropdown -->
                    <Dropdown v-else-if="field.type === 'select'" v-model="linkAccountForm.account_data[field.name]"
                        :options="field.options" optionLabel="label" optionValue="value"
                        :placeholder="field.placeholder || 'Select...'" class="w-full"
                        :class="{ 'p-invalid': linkAccountForm.errors[`account_data.${field.name}`] }" />

                    <!-- Textarea -->
                    <Textarea v-else-if="field.type === 'textarea'" v-model="linkAccountForm.account_data[field.name]"
                        class="w-full" :placeholder="field.placeholder || ''" rows="3"
                        :class="{ 'p-invalid': linkAccountForm.errors[`account_data.${field.name}`] }" />

                    <small v-if="field.help_text" class="text-gray-500">{{ field.help_text }}</small>
                    <small v-if="linkAccountForm.errors[`account_data.${field.name}`]" class="block mt-1 p-error">
                        {{ linkAccountForm.errors[`account_data.${field.name}`] }}
                    </small>
                </div>

                <!-- Limit Error -->
                <Message v-if="linkAccountForm.errors.limit" severity="error" :closable="false">
                    {{ linkAccountForm.errors.limit }}
                </Message>

                <!-- Info -->
                <Message severity="info" :closable="false">
                    <i class="mr-2 pi pi-info-circle"></i>
                    Linked accounts require verification before use. This typically takes 1-2 business days.
                </Message>
            </form>

            <template #footer>
                <Button label="Cancel" severity="secondary" @click="closeLinkAccountDialog"
                    :disabled="linkAccountForm.processing" />
                <Button label="Link Account" icon="pi pi-link" @click="submitLinkAccount"
                    :loading="linkAccountForm.processing" />
            </template>
        </Dialog>
    </div>
</template>
