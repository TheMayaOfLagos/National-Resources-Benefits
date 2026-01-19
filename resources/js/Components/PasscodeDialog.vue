<script setup>
import { ref, computed, watch, nextTick } from 'vue';
import Modal from '@/Components/Modal.vue';
import PrimaryButton from '@/Components/PrimaryButton.vue';
import SecondaryButton from '@/Components/SecondaryButton.vue';
import InputError from '@/Components/InputError.vue';
import axios from 'axios';

const props = defineProps({
    show: {
        type: Boolean,
        default: false,
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

const emit = defineEmits(['close', 'verified', 'setup-required']);

// State
const mode = ref('passcode'); // 'passcode', 'otp', 'setup'
const passcode = ref(['', '', '', '', '', '']);
const otp = ref(['', '', '', '', '', '']);
const error = ref('');
const attemptsRemaining = ref(5);
const isLoading = ref(false);
const otpSent = ref(false);
const otpExpiresIn = ref(0);
const isLocked = ref(false);
const lockoutRemaining = ref(0);

// Refs for input focus
const passcodeInputs = ref([]);
const otpInputs = ref([]);

// Computed
const passcodeValue = computed(() => passcode.value.join(''));
const otpValue = computed(() => otp.value.join(''));
const isPasscodeComplete = computed(() => passcode.value.every(d => d !== ''));
const isOtpComplete = computed(() => otp.value.every(d => d !== ''));

const needsSetup = computed(() => {
    return props.passcodeStatus.requires_passcode && !props.passcodeStatus.has_passcode;
});

// Watch for show changes
watch(() => props.show, (newVal) => {
    if (newVal) {
        resetState();
        if (props.passcodeStatus.is_locked) {
            isLocked.value = true;
            lockoutRemaining.value = props.passcodeStatus.lockout_remaining;
        }
        if (needsSetup.value) {
            mode.value = 'setup';
        }
        nextTick(() => {
            if (mode.value === 'passcode' && passcodeInputs.value[0]) {
                passcodeInputs.value[0].focus();
            }
        });
    }
});

// Methods
function resetState() {
    passcode.value = ['', '', '', '', '', ''];
    otp.value = ['', '', '', '', '', ''];
    error.value = '';
    attemptsRemaining.value = 5;
    isLoading.value = false;
    otpSent.value = false;
    isLocked.value = false;
    lockoutRemaining.value = 0;
    mode.value = needsSetup.value ? 'setup' : 'passcode';
}

function handlePasscodeInput(index, event) {
    const value = event.target.value;

    // Only allow digits
    if (!/^\d*$/.test(value)) {
        event.target.value = passcode.value[index];
        return;
    }

    // Handle paste
    if (value.length > 1) {
        const digits = value.replace(/\D/g, '').slice(0, 6);
        for (let i = 0; i < 6; i++) {
            passcode.value[i] = digits[i] || '';
        }
        const nextIndex = Math.min(digits.length, 5);
        passcodeInputs.value[nextIndex]?.focus();
        return;
    }

    passcode.value[index] = value;

    // Auto-advance to next input
    if (value && index < 5) {
        passcodeInputs.value[index + 1]?.focus();
    }
}

function handlePasscodeKeydown(index, event) {
    if (event.key === 'Backspace' && !passcode.value[index] && index > 0) {
        passcodeInputs.value[index - 1]?.focus();
    }
}

function handleOtpInput(index, event) {
    const value = event.target.value;

    if (!/^\d*$/.test(value)) {
        event.target.value = otp.value[index];
        return;
    }

    if (value.length > 1) {
        const digits = value.replace(/\D/g, '').slice(0, 6);
        for (let i = 0; i < 6; i++) {
            otp.value[i] = digits[i] || '';
        }
        const nextIndex = Math.min(digits.length, 5);
        otpInputs.value[nextIndex]?.focus();
        return;
    }

    otp.value[index] = value;

    if (value && index < 5) {
        otpInputs.value[index + 1]?.focus();
    }
}

function handleOtpKeydown(index, event) {
    if (event.key === 'Backspace' && !otp.value[index] && index > 0) {
        otpInputs.value[index - 1]?.focus();
    }
}

async function verifyPasscode() {
    if (!isPasscodeComplete.value) return;

    isLoading.value = true;
    error.value = '';

    try {
        const response = await axios.post(route('withdraw.verify-passcode'), {
            passcode: passcodeValue.value,
        });

        if (response.data.success) {
            emit('verified', { method: 'passcode' });
            emit('close');
        }
    } catch (err) {
        if (err.response?.status === 423) {
            isLocked.value = true;
            lockoutRemaining.value = err.response.data.lockout_remaining;
            error.value = err.response.data.message;
        } else if (err.response?.status === 401) {
            attemptsRemaining.value = err.response.data.attempts_remaining ?? 0;
            error.value = `Incorrect passcode. ${attemptsRemaining.value} attempts remaining.`;
            passcode.value = ['', '', '', '', '', ''];
            nextTick(() => passcodeInputs.value[0]?.focus());

            if (err.response.data.locked) {
                isLocked.value = true;
                lockoutRemaining.value = 15;
            }
        } else {
            error.value = err.response?.data?.message || 'Verification failed. Please try again.';
        }
    } finally {
        isLoading.value = false;
    }
}

async function sendOtp() {
    isLoading.value = true;
    error.value = '';

    try {
        const response = await axios.post(route('withdraw.send-otp'));

        if (response.data.success) {
            otpSent.value = true;
            otpExpiresIn.value = response.data.expires_in;
            mode.value = 'otp';
            nextTick(() => otpInputs.value[0]?.focus());
        }
    } catch (err) {
        if (err.response?.status === 423) {
            isLocked.value = true;
            lockoutRemaining.value = err.response.data.lockout_remaining;
            error.value = err.response.data.message;
        } else {
            error.value = err.response?.data?.message || 'Failed to send OTP. Please try again.';
        }
    } finally {
        isLoading.value = false;
    }
}

async function verifyOtp() {
    if (!isOtpComplete.value) return;

    isLoading.value = true;
    error.value = '';

    try {
        const response = await axios.post(route('withdraw.verify-otp'), {
            otp: otpValue.value,
        });

        if (response.data.success) {
            emit('verified', { method: 'otp' });
            emit('close');
        }
    } catch (err) {
        if (err.response?.status === 423) {
            isLocked.value = true;
            lockoutRemaining.value = err.response.data.lockout_remaining;
            error.value = err.response.data.message;
        } else if (err.response?.status === 401) {
            error.value = 'Invalid or expired OTP. Please try again.';
            otp.value = ['', '', '', '', '', ''];
            nextTick(() => otpInputs.value[0]?.focus());
        } else {
            error.value = err.response?.data?.message || 'Verification failed. Please try again.';
        }
    } finally {
        isLoading.value = false;
    }
}

function goToSetup() {
    emit('setup-required');
    emit('close');
}

function switchToOtp() {
    mode.value = 'otp';
    error.value = '';
    if (!otpSent.value) {
        sendOtp();
    } else {
        nextTick(() => otpInputs.value[0]?.focus());
    }
}

function switchToPasscode() {
    mode.value = 'passcode';
    error.value = '';
    passcode.value = ['', '', '', '', '', ''];
    nextTick(() => passcodeInputs.value[0]?.focus());
}

function close() {
    emit('close');
}
</script>

<template>
    <Modal :show="show" max-width="md" @close="close">
        <div class="p-6 bg-white dark:bg-gray-800">
            <!-- Header -->
            <div class="mb-6 text-center">
                <div class="flex items-center justify-center w-12 h-12 mx-auto mb-4 bg-blue-100 rounded-full dark:bg-blue-900/50">
                    <svg class="w-6 h-6 text-blue-600 dark:text-blue-400" fill="none" viewBox="0 0 24 24" stroke-width="1.5"
                        stroke="currentColor">
                        <path stroke-linecap="round" stroke-linejoin="round"
                            d="M16.5 10.5V6.75a4.5 4.5 0 1 0-9 0v3.75m-.75 11.25h10.5a2.25 2.25 0 0 0 2.25-2.25v-6.75a2.25 2.25 0 0 0-2.25-2.25H6.75a2.25 2.25 0 0 0-2.25 2.25v6.75a2.25 2.25 0 0 0 2.25 2.25Z" />
                    </svg>
                </div>
                <h3 class="text-lg font-semibold text-gray-900 dark:text-white">
                    {{ mode === 'setup' ? 'Setup Required' : 'Verify Withdrawal' }}
                </h3>
                <p class="mt-1 text-sm text-gray-500 dark:text-gray-400">
                    <template v-if="mode === 'setup'">
                        Please set up your withdrawal passcode to continue.
                    </template>
                    <template v-else-if="mode === 'passcode'">
                        Enter your 6-digit withdrawal passcode to confirm.
                    </template>
                    <template v-else>
                        Enter the 6-digit code sent to your email.
                    </template>
                </p>
            </div>

            <!-- Locked State -->
            <div v-if="isLocked" class="py-6 text-center">
                <div class="flex items-center justify-center w-16 h-16 mx-auto mb-4 bg-red-100 rounded-full dark:bg-red-900/50">
                    <svg class="w-8 h-8 text-red-600 dark:text-red-400" fill="none" viewBox="0 0 24 24" stroke-width="1.5"
                        stroke="currentColor">
                        <path stroke-linecap="round" stroke-linejoin="round"
                            d="M12 9v3.75m9-.75a9 9 0 1 1-18 0 9 9 0 0 1 18 0Zm-9 3.75h.008v.008H12v-.008Z" />
                    </svg>
                </div>
                <h4 class="mb-2 text-lg font-medium text-gray-900 dark:text-white">Account Temporarily Locked</h4>
                <p class="mb-4 text-sm text-gray-500 dark:text-gray-400">
                    Too many failed attempts. Please try again in {{ lockoutRemaining }} minutes.
                </p>
                <SecondaryButton @click="close">
                    Close
                </SecondaryButton>
            </div>

            <!-- Setup Required State -->
            <div v-else-if="mode === 'setup'" class="py-4 text-center">
                <div class="p-4 mb-6 border rounded-lg bg-amber-50 dark:bg-amber-900/30 border-amber-200 dark:border-amber-700">
                    <p class="text-sm text-amber-800 dark:text-amber-300">
                        Your account requires a withdrawal passcode for security. Please set one up in your security
                        settings before
                        proceeding.
                    </p>
                </div>
                <div class="flex justify-center space-x-3">
                    <SecondaryButton @click="close">
                        Cancel
                    </SecondaryButton>
                    <PrimaryButton @click="goToSetup">
                        Go to Security Settings
                    </PrimaryButton>
                </div>
            </div>

            <!-- Passcode Entry -->
            <div v-else-if="mode === 'passcode'" class="space-y-6">
                <div class="flex justify-center space-x-2">
                    <input v-for="(digit, index) in passcode" :key="'passcode-' + index"
                        :ref="el => passcodeInputs[index] = el" type="text" inputmode="numeric" maxlength="6"
                        :value="digit" @input="handlePasscodeInput(index, $event)"
                        @keydown="handlePasscodeKeydown(index, $event)"
                        class="w-12 text-2xl font-semibold text-center text-gray-900 bg-white border border-gray-300 rounded-lg h-14 dark:border-gray-600 focus:ring-2 focus:ring-blue-500 focus:border-blue-500 dark:bg-gray-700 dark:text-white"
                        :disabled="isLoading" />
                </div>

                <InputError :message="error" class="text-center" />

                <div class="flex flex-col space-y-3">
                    <PrimaryButton @click="verifyPasscode" :disabled="!isPasscodeComplete || isLoading"
                        class="justify-center w-full">
                        <span v-if="isLoading" class="flex items-center">
                            <svg class="w-4 h-4 mr-2 -ml-1 animate-spin" fill="none" viewBox="0 0 24 24">
                                <circle class="opacity-25" cx="12" cy="12" r="10" stroke="currentColor"
                                    stroke-width="4"></circle>
                                <path class="opacity-75" fill="currentColor"
                                    d="M4 12a8 8 0 018-8V0C5.373 0 0 5.373 0 12h4zm2 5.291A7.962 7.962 0 014 12H0c0 3.042 1.135 5.824 3 7.938l3-2.647z">
                                </path>
                            </svg>
                            Verifying...
                        </span>
                        <span v-else>Verify Passcode</span>
                    </PrimaryButton>

                    <button type="button" @click="switchToOtp" :disabled="isLoading"
                        class="text-sm text-blue-600 dark:text-blue-400 hover:text-blue-800 dark:hover:text-blue-300 disabled:opacity-50">
                        Use Email OTP Instead
                    </button>
                </div>
            </div>

            <!-- OTP Entry -->
            <div v-else-if="mode === 'otp'" class="space-y-6">
                <div v-if="!otpSent" class="py-4 text-center">
                    <div class="flex items-center justify-center">
                        <svg class="w-8 h-8 text-blue-600 animate-spin dark:text-blue-400" fill="none" viewBox="0 0 24 24">
                            <circle class="opacity-25" cx="12" cy="12" r="10" stroke="currentColor" stroke-width="4">
                            </circle>
                            <path class="opacity-75" fill="currentColor"
                                d="M4 12a8 8 0 018-8V0C5.373 0 0 5.373 0 12h4zm2 5.291A7.962 7.962 0 014 12H0c0 3.042 1.135 5.824 3 7.938l3-2.647z">
                            </path>
                        </svg>
                    </div>
                    <p class="mt-2 text-sm text-gray-500 dark:text-gray-400">Sending OTP to your email...</p>
                </div>

                <template v-else>
                    <div class="flex justify-center space-x-2">
                        <input v-for="(digit, index) in otp" :key="'otp-' + index" :ref="el => otpInputs[index] = el"
                            type="text" inputmode="numeric" maxlength="6" :value="digit"
                            @input="handleOtpInput(index, $event)" @keydown="handleOtpKeydown(index, $event)"
                            class="w-12 text-2xl font-semibold text-center text-gray-900 bg-white border border-gray-300 rounded-lg h-14 dark:border-gray-600 focus:ring-2 focus:ring-blue-500 focus:border-blue-500 dark:bg-gray-700 dark:text-white"
                            :disabled="isLoading" />
                    </div>

                    <p class="text-sm text-center text-gray-500 dark:text-gray-400">
                        Code expires in {{ otpExpiresIn }} minutes
                    </p>

                    <InputError :message="error" class="text-center" />

                    <div class="flex flex-col space-y-3">
                        <PrimaryButton @click="verifyOtp" :disabled="!isOtpComplete || isLoading"
                            class="justify-center w-full">
                            <span v-if="isLoading" class="flex items-center">
                                <svg class="w-4 h-4 mr-2 -ml-1 animate-spin" fill="none" viewBox="0 0 24 24">
                                    <circle class="opacity-25" cx="12" cy="12" r="10" stroke="currentColor"
                                        stroke-width="4"></circle>
                                    <path class="opacity-75" fill="currentColor"
                                        d="M4 12a8 8 0 018-8V0C5.373 0 0 5.373 0 12h4zm2 5.291A7.962 7.962 0 014 12H0c0 3.042 1.135 5.824 3 7.938l3-2.647z">
                                    </path>
                                </svg>
                                Verifying...
                            </span>
                            <span v-else>Verify OTP</span>
                        </PrimaryButton>

                        <div class="flex justify-between">
                            <button type="button" @click="sendOtp" :disabled="isLoading"
                                class="text-sm text-blue-600 dark:text-blue-400 hover:text-blue-800 dark:hover:text-blue-300 disabled:opacity-50">
                                Resend Code
                            </button>
                            <button v-if="passcodeStatus.has_passcode" type="button" @click="switchToPasscode"
                                :disabled="isLoading"
                                class="text-sm text-blue-600 dark:text-blue-400 hover:text-blue-800 dark:hover:text-blue-300 disabled:opacity-50">
                                Use Passcode
                            </button>
                        </div>
                    </div>
                </template>
            </div>

            <!-- Cancel Button (for non-locked states) -->
            <div v-if="!isLocked && mode !== 'setup'" class="pt-4 mt-4 border-t border-gray-200 dark:border-gray-700">
                <button type="button" @click="close"
                    class="w-full text-sm text-center text-gray-500 dark:text-gray-400 hover:text-gray-700 dark:hover:text-gray-300">
                    Cancel Withdrawal
                </button>
            </div>
        </div>
    </Modal>
</template>
