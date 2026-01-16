<script setup>
import { ref, computed, onMounted, nextTick } from 'vue';
import { Head, useForm, router } from '@inertiajs/vue3';
import GuestLayout from '@/Layouts/GuestLayout.vue';
import InputText from 'primevue/inputtext';
import Button from 'primevue/button';
import Message from 'primevue/message';
import { useToast } from 'primevue/usetoast';

const props = defineProps({
    user_name: String,
});

const toast = useToast();
const codeInput = ref(null);
const recoveryCodeInput = ref(null);

// Toggle between code and recovery mode
const useRecoveryCode = ref(false);

// Form for TOTP code
const codeForm = useForm({
    code: '',
});

// Form for recovery code
const recoveryForm = useForm({
    recovery_code: '',
});

const isCodeValid = computed(() => {
    return codeForm.code && codeForm.code.length === 6;
});

const toggleRecoveryMode = () => {
    useRecoveryCode.value = !useRecoveryCode.value;
    codeForm.reset();
    recoveryForm.reset();

    nextTick(() => {
        if (useRecoveryCode.value && recoveryCodeInput.value) {
            recoveryCodeInput.value.$el.querySelector('input')?.focus();
        } else if (!useRecoveryCode.value && codeInput.value) {
            codeInput.value.$el.querySelector('input')?.focus();
        }
    });
};

const verifyCode = () => {
    codeForm.post(route('auth.two-factor.verify'), {
        preserveScroll: true,
        onError: () => {
            toast.add({
                severity: 'error',
                summary: 'Invalid Code',
                detail: 'The code you entered is invalid. Please try again.',
                life: 5000
            });
            codeForm.reset();
        },
    });
};

const verifyRecoveryCode = () => {
    recoveryForm.transform((data) => ({
        code: null,
        recovery_code: data.recovery_code,
    })).post(route('auth.two-factor.verify'), {
        preserveScroll: true,
        onError: () => {
            toast.add({
                severity: 'error',
                summary: 'Invalid Recovery Code',
                detail: 'The recovery code you entered is invalid or has already been used.',
                life: 5000
            });
            recoveryForm.reset();
        },
    });
};

const cancelLogin = () => {
    router.post(route('logout'));
};

// Auto-focus on mount
onMounted(() => {
    nextTick(() => {
        if (codeInput.value) {
            codeInput.value.$el.querySelector('input')?.focus();
        }
    });
});
</script>

<template>
    <Head title="Two-Factor Authentication" />

    <GuestLayout>
        <div class="w-full max-w-md mx-auto">
            <!-- Header -->
            <div class="text-center mb-8">
                <div class="w-20 h-20 mx-auto rounded-full bg-purple-100 dark:bg-purple-900/50 flex items-center justify-center mb-4">
                    <i class="pi pi-shield text-4xl text-purple-600 dark:text-purple-400"></i>
                </div>
                <h1 class="text-2xl font-bold text-gray-900 dark:text-white">Two-Factor Authentication</h1>
                <p class="text-gray-600 dark:text-gray-400 mt-2">
                    <span v-if="user_name">Hi {{ user_name }}, </span>
                    <span v-if="!useRecoveryCode">
                        Please enter the 6-digit code from your authenticator app.
                    </span>
                    <span v-else>
                        Please enter one of your recovery codes.
                    </span>
                </p>
            </div>

            <!-- TOTP Code Form -->
            <form v-if="!useRecoveryCode" @submit.prevent="verifyCode" class="space-y-6">
                <div class="bg-white dark:bg-gray-800 rounded-xl shadow-sm border border-gray-200 dark:border-gray-700 p-6">
                    <div class="space-y-4">
                        <div>
                            <label class="block text-sm font-medium text-gray-700 dark:text-gray-300 mb-2">
                                Authentication Code
                            </label>
                            <InputText
                                ref="codeInput"
                                v-model="codeForm.code"
                                class="w-full text-center text-2xl tracking-widest font-mono"
                                :class="{ 'p-invalid': codeForm.errors.code }"
                                placeholder="000000"
                                maxlength="6"
                                autocomplete="one-time-code"
                                inputmode="numeric"
                            />
                            <small v-if="codeForm.errors.code" class="text-red-500">
                                {{ codeForm.errors.code }}
                            </small>
                        </div>

                        <Button
                            type="submit"
                            label="Verify"
                            icon="pi pi-check"
                            class="w-full"
                            :loading="codeForm.processing"
                            :disabled="!isCodeValid || codeForm.processing"
                        />
                    </div>
                </div>
            </form>

            <!-- Recovery Code Form -->
            <form v-else @submit.prevent="verifyRecoveryCode" class="space-y-6">
                <div class="bg-white dark:bg-gray-800 rounded-xl shadow-sm border border-gray-200 dark:border-gray-700 p-6">
                    <div class="space-y-4">
                        <div class="p-3 rounded-lg bg-yellow-50 dark:bg-yellow-900/20 border border-yellow-200 dark:border-yellow-800">
                            <div class="flex items-start gap-2">
                                <i class="pi pi-exclamation-triangle text-yellow-500 mt-0.5 text-sm"></i>
                                <p class="text-xs text-yellow-700 dark:text-yellow-300">
                                    Recovery codes can only be used once. After using this code, it will be invalidated.
                                </p>
                            </div>
                        </div>

                        <div>
                            <label class="block text-sm font-medium text-gray-700 dark:text-gray-300 mb-2">
                                Recovery Code
                            </label>
                            <InputText
                                ref="recoveryCodeInput"
                                v-model="recoveryForm.recovery_code"
                                class="w-full font-mono"
                                :class="{ 'p-invalid': recoveryForm.errors.recovery_code }"
                                placeholder="Enter your recovery code"
                            />
                            <small v-if="recoveryForm.errors.recovery_code" class="text-red-500">
                                {{ recoveryForm.errors.recovery_code }}
                            </small>
                        </div>

                        <Button
                            type="submit"
                            label="Verify Recovery Code"
                            icon="pi pi-check"
                            class="w-full"
                            :loading="recoveryForm.processing"
                            :disabled="!recoveryForm.recovery_code || recoveryForm.processing"
                        />
                    </div>
                </div>
            </form>

            <!-- Toggle & Cancel Actions -->
            <div class="mt-6 flex flex-col items-center gap-4">
                <button
                    type="button"
                    @click="toggleRecoveryMode"
                    class="text-sm text-blue-600 dark:text-blue-400 hover:underline focus:outline-none"
                >
                    <span v-if="!useRecoveryCode">
                        <i class="pi pi-key mr-1"></i>
                        Use a recovery code instead
                    </span>
                    <span v-else>
                        <i class="pi pi-mobile mr-1"></i>
                        Use authenticator app instead
                    </span>
                </button>

                <button
                    type="button"
                    @click="cancelLogin"
                    class="text-sm text-gray-500 dark:text-gray-400 hover:text-gray-700 dark:hover:text-gray-300 focus:outline-none"
                >
                    <i class="pi pi-arrow-left mr-1"></i>
                    Cancel and return to login
                </button>
            </div>
        </div>
    </GuestLayout>
</template>

<style scoped>
:deep(.p-inputtext) {
    width: 100%;
}
</style>
