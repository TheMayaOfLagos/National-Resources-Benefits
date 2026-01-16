<script setup>
import { Head, Link, useForm, usePage, router } from '@inertiajs/vue3';
import { ref, computed } from 'vue';
import DashboardLayout from '@/Layouts/DashboardLayout.vue';
import InputText from 'primevue/inputtext';
import Password from 'primevue/password';
import Button from 'primevue/button';
import Dialog from 'primevue/dialog';
import Message from 'primevue/message';
import DataTable from 'primevue/datatable';
import Column from 'primevue/column';
import Tag from 'primevue/tag';
import { useToast } from 'primevue/usetoast';

const props = defineProps({
    user: Object,
    sessions: {
        type: Array,
        default: () => [],
    },
});

const page = usePage();
const toast = useToast();

// Password form
const passwordForm = useForm({
    current_password: '',
    password: '',
    password_confirmation: '',
});

// Sessions form
const sessionsForm = useForm({
    password: '',
});

// Delete account form
const deleteForm = useForm({
    password: '',
});

// Dialogs
const showLogoutOthersDialog = ref(false);
const showDeleteAccountDialog = ref(false);
const showEnableTwoFactorDialog = ref(false);
const showConfirmTwoFactorDialog = ref(false);
const showRecoveryCodesDialog = ref(false);
const showDisableTwoFactorDialog = ref(false);

// Two-Factor state
const twoFactorQrCode = ref(null);
const twoFactorSecret = ref(null);
const recoveryCodes = ref([]);
const twoFactorLoading = ref(false);

// Two-Factor forms
const enableTwoFactorForm = useForm({
    password: '',
});

const confirmTwoFactorForm = useForm({
    code: '',
});

const disableTwoFactorForm = useForm({
    password: '',
});

// Computed
const twoFactorEnabled = computed(() => props.user?.two_factor_enabled && props.user?.two_factor_confirmed_at);

// Two-Factor Methods
const enableTwoFactor = () => {
    enableTwoFactorForm.post(route('auth.two-factor.enable'), {
        preserveScroll: true,
        onSuccess: () => {
            // Access flash data from usePage after the request completes
            const flash = usePage().props.flash;
            twoFactorQrCode.value = flash?.qr_code_svg;
            twoFactorSecret.value = flash?.secret;
            showEnableTwoFactorDialog.value = false;
            showConfirmTwoFactorDialog.value = true;
            enableTwoFactorForm.reset();
        },
        onError: () => {
            toast.add({ severity: 'error', summary: 'Error', detail: 'Failed to enable two-factor authentication', life: 3000 });
        },
    });
};

const confirmTwoFactor = () => {
    confirmTwoFactorForm.post(route('auth.two-factor.confirm'), {
        preserveScroll: true,
        onSuccess: () => {
            const flash = usePage().props.flash;
            recoveryCodes.value = flash?.recovery_codes || [];
            showConfirmTwoFactorDialog.value = false;
            showRecoveryCodesDialog.value = true;
            confirmTwoFactorForm.reset();
            toast.add({ severity: 'success', summary: 'Success', detail: 'Two-factor authentication enabled successfully', life: 3000 });
        },
        onError: () => {
            toast.add({ severity: 'error', summary: 'Error', detail: 'Invalid verification code', life: 3000 });
        },
    });
};

const disableTwoFactor = () => {
    disableTwoFactorForm.post(route('auth.two-factor.disable'), {
        preserveScroll: true,
        onSuccess: () => {
            showDisableTwoFactorDialog.value = false;
            disableTwoFactorForm.reset();
            twoFactorQrCode.value = null;
            twoFactorSecret.value = null;
            recoveryCodes.value = [];
            toast.add({ severity: 'success', summary: 'Success', detail: 'Two-factor authentication disabled', life: 3000 });
        },
        onError: () => {
            toast.add({ severity: 'error', summary: 'Error', detail: 'Failed to disable two-factor authentication', life: 3000 });
        },
    });
};

const regenerateRecoveryCodes = () => {
    twoFactorLoading.value = true;
    router.post(route('auth.two-factor.recovery-codes'), {}, {
        preserveScroll: true,
        onSuccess: () => {
            const flash = usePage().props.flash;
            recoveryCodes.value = flash?.recovery_codes || [];
            showRecoveryCodesDialog.value = true;
            toast.add({ severity: 'success', summary: 'Success', detail: 'Recovery codes regenerated', life: 3000 });
        },
        onError: () => {
            toast.add({ severity: 'error', summary: 'Error', detail: 'Failed to regenerate recovery codes', life: 3000 });
        },
        onFinish: () => {
            twoFactorLoading.value = false;
        },
    });
};

const copyRecoveryCodes = () => {
    const codes = recoveryCodes.value.join('\n');
    navigator.clipboard.writeText(codes).then(() => {
        toast.add({ severity: 'success', summary: 'Copied', detail: 'Recovery codes copied to clipboard', life: 3000 });
    });
};

const copySecret = () => {
    if (twoFactorSecret.value) {
        navigator.clipboard.writeText(twoFactorSecret.value).then(() => {
            toast.add({ severity: 'success', summary: 'Copied', detail: 'Secret key copied to clipboard', life: 3000 });
        });
    }
};

const downloadRecoveryCodes = () => {
    const codes = recoveryCodes.value.join('\n');
    const blob = new Blob([codes], { type: 'text/plain' });
    const url = URL.createObjectURL(blob);
    const a = document.createElement('a');
    a.href = url;
    a.download = 'recovery-codes.txt';
    a.click();
    URL.revokeObjectURL(url);
};

const updatePassword = () => {
    passwordForm.put(route('profile.password.update'), {
        preserveScroll: true,
        onSuccess: () => {
            passwordForm.reset();
            toast.add({ severity: 'success', summary: 'Success', detail: 'Password updated successfully', life: 3000 });
        },
        onError: () => {
            if (passwordForm.errors.current_password) {
                passwordForm.reset('current_password');
            }
            if (passwordForm.errors.password) {
                passwordForm.reset('password', 'password_confirmation');
            }
            toast.add({ severity: 'error', summary: 'Error', detail: 'Failed to update password', life: 3000 });
        },
    });
};

const logoutOtherSessions = () => {
    sessionsForm.post(route('profile.sessions.destroy'), {
        preserveScroll: true,
        onSuccess: () => {
            sessionsForm.reset();
            showLogoutOthersDialog.value = false;
            toast.add({ severity: 'success', summary: 'Success', detail: 'Other sessions logged out', life: 3000 });
        },
        onError: () => {
            toast.add({ severity: 'error', summary: 'Error', detail: 'Failed to logout other sessions', life: 3000 });
        },
    });
};

const deleteAccount = () => {
    deleteForm.delete(route('profile.destroy'), {
        preserveScroll: true,
        onSuccess: () => {
            showDeleteAccountDialog.value = false;
        },
        onError: () => {
            toast.add({ severity: 'error', summary: 'Error', detail: 'Failed to delete account', life: 3000 });
        },
    });
};

const getDeviceIcon = (device) => {
    switch (device) {
        case 'Desktop': return 'pi pi-desktop';
        case 'Mobile': return 'pi pi-mobile';
        case 'Tablet': return 'pi pi-tablet';
        default: return 'pi pi-globe';
    }
};

const getBrowserIcon = (browser) => {
    const browserLower = browser?.toLowerCase() || '';
    if (browserLower.includes('chrome')) return 'pi pi-google';
    if (browserLower.includes('firefox')) return 'pi pi-firefox';
    if (browserLower.includes('safari')) return 'pi pi-apple';
    if (browserLower.includes('edge')) return 'pi pi-microsoft';
    return 'pi pi-globe';
};
</script>

<template>

    <Head title="Security Settings" />

    <DashboardLayout>
        <template #header>Security Settings</template>

        <div class="max-w-4xl mx-auto space-y-6">
            <!-- Profile Navigation Tabs -->
            <div class="bg-white border border-gray-100 shadow-sm dark:bg-gray-800 rounded-xl dark:border-gray-700">
                <div class="flex border-b border-gray-100 dark:border-gray-700">
                    <Link :href="route('profile.edit')"
                        class="px-6 py-4 text-sm font-medium text-gray-500 border-b-2 border-transparent dark:text-gray-400 hover:text-gray-700 dark:hover:text-gray-300 hover:border-gray-300">
                    <i class="mr-2 pi pi-user"></i>
                    Profile
                    </Link>
                    <Link :href="route('profile.security')"
                        class="px-6 py-4 text-sm font-medium text-blue-600 border-b-2 border-blue-500 dark:text-blue-400">
                    <i class="mr-2 pi pi-shield"></i>
                    Security
                    </Link>
                    <Link :href="route('linked-accounts.index')"
                        class="px-6 py-4 text-sm font-medium text-gray-500 border-b-2 border-transparent dark:text-gray-400 hover:text-gray-700 dark:hover:text-gray-300 hover:border-gray-300">
                    <i class="mr-2 pi pi-link"></i>
                    Linked Accounts
                    </Link>
                </div>
            </div>

            <!-- Success Message -->
            <Message v-if="page.props.flash?.success" severity="success" :closable="true">
                {{ page.props.flash.success }}
            </Message>

            <!-- Change Password -->
            <div class="p-6 bg-white border border-gray-100 shadow-sm dark:bg-gray-800 rounded-xl dark:border-gray-700">
                <div class="flex items-start justify-between mb-6">
                    <div>
                        <h3 class="text-lg font-semibold text-gray-900 dark:text-white">Change Password</h3>
                        <p class="mt-1 text-sm text-gray-500 dark:text-gray-400">
                            Ensure your account is using a long, random password to stay secure.
                        </p>
                    </div>
                    <div
                        class="flex items-center justify-center w-10 h-10 bg-blue-100 rounded-full dark:bg-blue-900/50">
                        <i class="text-blue-600 pi pi-lock dark:text-blue-400"></i>
                    </div>
                </div>

                <form @submit.prevent="updatePassword" class="max-w-md space-y-4">
                    <div>
                        <label class="block mb-2 text-sm font-medium text-gray-700 dark:text-gray-300">
                            Current Password
                        </label>
                        <Password v-model="passwordForm.current_password" class="w-full"
                            :class="{ 'p-invalid': passwordForm.errors.current_password }" :feedback="false" toggleMask
                            inputClass="w-full" />
                        <small v-if="passwordForm.errors.current_password" class="text-red-500">
                            {{ passwordForm.errors.current_password }}
                        </small>
                    </div>

                    <div>
                        <label class="block mb-2 text-sm font-medium text-gray-700 dark:text-gray-300">
                            New Password
                        </label>
                        <Password v-model="passwordForm.password" class="w-full"
                            :class="{ 'p-invalid': passwordForm.errors.password }" toggleMask inputClass="w-full"
                            promptLabel="Enter a new password" weakLabel="Weak" mediumLabel="Medium"
                            strongLabel="Strong" />
                        <small v-if="passwordForm.errors.password" class="text-red-500">
                            {{ passwordForm.errors.password }}
                        </small>
                    </div>

                    <div>
                        <label class="block mb-2 text-sm font-medium text-gray-700 dark:text-gray-300">
                            Confirm New Password
                        </label>
                        <Password v-model="passwordForm.password_confirmation" class="w-full" :feedback="false"
                            toggleMask inputClass="w-full" />
                    </div>

                    <div class="pt-2">
                        <Button type="submit" label="Update Password" icon="pi pi-check"
                            :loading="passwordForm.processing" :disabled="passwordForm.processing" />
                    </div>
                </form>
            </div>

            <!-- Browser Sessions -->
            <div class="p-6 bg-white border border-gray-100 shadow-sm dark:bg-gray-800 rounded-xl dark:border-gray-700">
                <div class="flex items-start justify-between mb-6">
                    <div>
                        <h3 class="text-lg font-semibold text-gray-900 dark:text-white">Browser Sessions</h3>
                        <p class="mt-1 text-sm text-gray-500 dark:text-gray-400">
                            Manage and logout your active sessions on other browsers and devices.
                        </p>
                    </div>
                    <div
                        class="flex items-center justify-center w-10 h-10 bg-green-100 rounded-full dark:bg-green-900/50">
                        <i class="text-green-600 pi pi-globe dark:text-green-400"></i>
                    </div>
                </div>

                <div v-if="sessions && sessions.length > 0" class="space-y-4">
                    <div v-for="session in sessions" :key="session.id"
                        class="flex items-center justify-between p-4 transition-colors border border-gray-100 rounded-lg dark:border-gray-700 hover:bg-gray-50 dark:hover:bg-gray-700/50">
                        <div class="flex items-center gap-4">
                            <div class="flex items-center justify-center w-12 h-12 rounded-full"
                                :class="session.is_current ? 'bg-green-100 dark:bg-green-900/50' : 'bg-gray-100 dark:bg-gray-700'">
                                <i :class="getDeviceIcon(session.device)" class="text-xl"
                                    :style="{ color: session.is_current ? '#22c55e' : '#6b7280' }"></i>
                            </div>
                            <div>
                                <div class="flex items-center gap-2">
                                    <span class="font-medium text-gray-900 dark:text-white">
                                        {{ session.browser }} on {{ session.platform }}
                                    </span>
                                    <Tag v-if="session.is_current" value="Current" severity="success" class="text-xs" />
                                </div>
                                <div class="mt-1 text-sm text-gray-500 dark:text-gray-400">
                                    <span>{{ session.ip_address }}</span>
                                    <span class="mx-2">•</span>
                                    <span>{{ session.last_active }}</span>
                                </div>
                            </div>
                        </div>
                    </div>

                    <div class="pt-4">
                        <Button label="Log Out Other Browser Sessions" icon="pi pi-sign-out" severity="secondary"
                            @click="showLogoutOthersDialog = true" />
                    </div>
                </div>

                <div v-else class="py-8 text-center">
                    <div
                        class="flex items-center justify-center w-16 h-16 mx-auto mb-4 bg-gray-100 rounded-full dark:bg-gray-700">
                        <i class="text-2xl text-gray-400 pi pi-desktop"></i>
                    </div>
                    <p class="text-gray-500 dark:text-gray-400">
                        Session tracking requires database session driver.
                    </p>
                </div>
            </div>

            <!-- Two-Factor Authentication -->
            <div class="p-6 bg-white border border-gray-100 shadow-sm dark:bg-gray-800 rounded-xl dark:border-gray-700">
                <div class="flex items-start justify-between">
                    <div>
                        <h3 class="text-lg font-semibold text-gray-900 dark:text-white">Two-Factor Authentication</h3>
                        <p class="mt-1 text-sm text-gray-500 dark:text-gray-400">
                            Add additional security to your account using two-factor authentication.
                        </p>
                    </div>
                    <div
                        class="flex items-center justify-center w-10 h-10 bg-purple-100 rounded-full dark:bg-purple-900/50">
                        <i class="text-purple-600 pi pi-mobile dark:text-purple-400"></i>
                    </div>
                </div>

                <!-- 2FA Enabled State -->
                <div v-if="twoFactorEnabled" class="mt-6">
                    <div
                        class="p-4 mb-4 border border-green-200 rounded-lg bg-green-50 dark:bg-green-900/20 dark:border-green-800">
                        <div class="flex items-center gap-3">
                            <div
                                class="flex items-center justify-center w-8 h-8 bg-green-200 rounded-full dark:bg-green-800">
                                <i class="text-green-600 pi pi-shield dark:text-green-400"></i>
                            </div>
                            <div>
                                <p class="text-sm font-medium text-green-700 dark:text-green-300">
                                    Two-factor authentication is enabled
                                </p>
                                <p class="mt-1 text-xs text-green-600 dark:text-green-400">
                                    Your account is secured with an authenticator app.
                                </p>
                            </div>
                        </div>
                    </div>

                    <div class="flex flex-wrap gap-3">
                        <Button label="View Recovery Codes" icon="pi pi-key" severity="secondary"
                            :loading="twoFactorLoading" @click="regenerateRecoveryCodes" />
                        <Button label="Disable 2FA" icon="pi pi-times" severity="danger" outlined
                            @click="showDisableTwoFactorDialog = true" />
                    </div>
                </div>

                <!-- 2FA Not Enabled State -->
                <div v-else class="mt-6">
                    <div
                        class="p-4 mb-4 border border-gray-200 rounded-lg bg-gray-50 dark:bg-gray-700/50 dark:border-gray-600">
                        <div class="flex items-start gap-3">
                            <div
                                class="flex items-center justify-center flex-shrink-0 w-8 h-8 bg-gray-200 rounded-full dark:bg-gray-600">
                                <i class="text-gray-500 pi pi-info-circle dark:text-gray-400"></i>
                            </div>
                            <div>
                                <p class="text-sm text-gray-600 dark:text-gray-300">
                                    Two-factor authentication is not enabled yet.
                                </p>
                                <p class="mt-1 text-xs text-gray-500 dark:text-gray-400">
                                    When enabled, you'll be prompted for a secure code during authentication.
                                    You can retrieve this code from your phone's authenticator app (Google
                                    Authenticator, Authy,
                                    etc.).
                                </p>
                            </div>
                        </div>
                    </div>

                    <Button label="Enable Two-Factor Authentication" icon="pi pi-shield"
                        @click="showEnableTwoFactorDialog = true" />
                </div>
            </div>

            <!-- Delete Account -->
            <div
                class="p-6 bg-white border border-red-200 shadow-sm dark:bg-gray-800 rounded-xl dark:border-red-900/50">
                <div class="flex items-start justify-between mb-6">
                    <div>
                        <h3 class="text-lg font-semibold text-red-600 dark:text-red-400">Delete Account</h3>
                        <p class="mt-1 text-sm text-gray-500 dark:text-gray-400">
                            Permanently delete your account and all associated data.
                        </p>
                    </div>
                    <div class="flex items-center justify-center w-10 h-10 bg-red-100 rounded-full dark:bg-red-900/50">
                        <i class="text-red-600 pi pi-trash dark:text-red-400"></i>
                    </div>
                </div>

                <div class="p-4 mb-6 border border-red-200 rounded-lg bg-red-50 dark:bg-red-900/20 dark:border-red-800">
                    <div class="flex items-start gap-3">
                        <i class="pi pi-exclamation-triangle text-red-500 mt-0.5"></i>
                        <div>
                            <p class="text-sm font-medium text-red-700 dark:text-red-300">Warning</p>
                            <p class="mt-1 text-sm text-red-600 dark:text-red-400">
                                Once your account is deleted, all of its resources and data will be permanently deleted.
                                Before deleting your account, please download any data or information that you wish to
                                retain.
                            </p>
                        </div>
                    </div>
                </div>

                <Button label="Delete Account" icon="pi pi-trash" severity="danger"
                    @click="showDeleteAccountDialog = true" />
            </div>
        </div>

        <!-- Logout Other Sessions Dialog -->
        <Dialog v-model:visible="showLogoutOthersDialog" header="Log Out Other Browser Sessions" :modal="true"
            :style="{ width: '400px' }">
            <p class="mb-4 text-gray-600 dark:text-gray-300">
                Please enter your password to confirm you would like to log out of your other browser sessions.
            </p>

            <div class="mb-4">
                <Password v-model="sessionsForm.password" class="w-full" :feedback="false" toggleMask
                    inputClass="w-full" placeholder="Enter your password" />
                <small v-if="sessionsForm.errors.password" class="text-red-500">
                    {{ sessionsForm.errors.password }}
                </small>
            </div>

            <template #footer>
                <Button label="Cancel" severity="secondary" @click="showLogoutOthersDialog = false" />
                <Button label="Log Out Other Sessions" :loading="sessionsForm.processing"
                    @click="logoutOtherSessions" />
            </template>
        </Dialog>

        <!-- Delete Account Dialog -->
        <Dialog v-model:visible="showDeleteAccountDialog" header="Delete Account" :modal="true"
            :style="{ width: '450px' }">
            <div class="mb-4 text-center">
                <div
                    class="flex items-center justify-center w-16 h-16 mx-auto mb-4 bg-red-100 rounded-full dark:bg-red-900/50">
                    <i class="text-3xl text-red-500 pi pi-exclamation-triangle"></i>
                </div>
                <p class="text-gray-600 dark:text-gray-300">
                    Are you sure you want to delete your account? This action cannot be undone.
                </p>
            </div>

            <div class="mb-4">
                <label class="block mb-2 text-sm font-medium text-gray-700 dark:text-gray-300">
                    Enter your password to confirm
                </label>
                <Password v-model="deleteForm.password" class="w-full" :feedback="false" toggleMask inputClass="w-full"
                    placeholder="Enter your password" />
                <small v-if="deleteForm.errors.password" class="text-red-500">
                    {{ deleteForm.errors.password }}
                </small>
            </div>

            <template #footer>
                <Button label="Cancel" severity="secondary" @click="showDeleteAccountDialog = false" />
                <Button label="Delete Account" severity="danger" :loading="deleteForm.processing"
                    @click="deleteAccount" />
            </template>
        </Dialog>

        <!-- Enable Two-Factor Dialog -->
        <Dialog v-model:visible="showEnableTwoFactorDialog" header="Enable Two-Factor Authentication" :modal="true"
            :style="{ width: '450px' }">
            <div class="mb-4 text-center">
                <div
                    class="flex items-center justify-center w-16 h-16 mx-auto mb-4 bg-purple-100 rounded-full dark:bg-purple-900/50">
                    <i class="text-3xl text-purple-600 pi pi-shield dark:text-purple-400"></i>
                </div>
                <p class="text-gray-600 dark:text-gray-300">
                    Enter your password to begin setting up two-factor authentication.
                </p>
            </div>

            <div class="mb-4">
                <label class="block mb-2 text-sm font-medium text-gray-700 dark:text-gray-300">
                    Current Password
                </label>
                <Password v-model="enableTwoFactorForm.password" class="w-full" :feedback="false" toggleMask
                    inputClass="w-full" placeholder="Enter your password" />
                <small v-if="enableTwoFactorForm.errors.password" class="text-red-500">
                    {{ enableTwoFactorForm.errors.password }}
                </small>
            </div>

            <template #footer>
                <Button label="Cancel" severity="secondary" @click="showEnableTwoFactorDialog = false" />
                <Button label="Continue" icon="pi pi-arrow-right" iconPos="right"
                    :loading="enableTwoFactorForm.processing" @click="enableTwoFactor" />
            </template>
        </Dialog>

        <!-- Confirm Two-Factor Dialog (QR Code Setup) -->
        <Dialog v-model:visible="showConfirmTwoFactorDialog" header="Setup Authenticator App" :modal="true"
            :closable="false" :style="{ width: '500px' }">
            <div class="space-y-4">
                <div class="p-4 border border-blue-200 rounded-lg bg-blue-50 dark:bg-blue-900/20 dark:border-blue-800">
                    <div class="flex items-start gap-3">
                        <i class="pi pi-info-circle text-blue-500 mt-0.5"></i>
                        <div class="text-sm text-blue-700 dark:text-blue-300">
                            <p class="mb-1 font-medium">Setup Instructions:</p>
                            <ol class="space-y-1 text-blue-600 list-decimal list-inside dark:text-blue-400">
                                <li>Download an authenticator app (Google Authenticator, Authy, etc.)</li>
                                <li>Scan the QR code below or enter the secret key manually</li>
                                <li>Enter the 6-digit code from your app to confirm</li>
                            </ol>
                        </div>
                    </div>
                </div>

                <!-- QR Code -->
                <div class="text-center" v-if="twoFactorQrCode">
                    <div class="inline-block p-4 bg-white border border-gray-200 rounded-lg">
                        <div v-html="twoFactorQrCode" class="qr-code"></div>
                    </div>
                </div>

                <!-- Secret Key -->
                <div v-if="twoFactorSecret"
                    class="p-3 border border-gray-200 rounded-lg bg-gray-50 dark:bg-gray-700/50 dark:border-gray-600">
                    <p class="mb-1 text-xs text-gray-500 dark:text-gray-400">Manual entry key:</p>
                    <div class="flex items-center justify-between gap-2">
                        <code
                            class="font-mono text-sm text-gray-900 break-all dark:text-white">{{ twoFactorSecret }}</code>
                        <Button icon="pi pi-copy" severity="secondary" text rounded size="small"
                            v-tooltip.top="'Copy to clipboard'" @click="copySecret" />
                    </div>
                </div>

                <!-- Confirmation Code Input -->
                <div>
                    <label class="block mb-2 text-sm font-medium text-gray-700 dark:text-gray-300">
                        Enter the 6-digit code from your app
                    </label>
                    <InputText v-model="confirmTwoFactorForm.code" class="w-full text-xl tracking-widest text-center"
                        placeholder="000000" maxlength="6" />
                    <small v-if="confirmTwoFactorForm.errors.code" class="text-red-500">
                        {{ confirmTwoFactorForm.errors.code }}
                    </small>
                </div>
            </div>

            <template #footer>
                <Button label="Cancel" severity="secondary"
                    @click="showConfirmTwoFactorDialog = false; twoFactorQrCode = null; twoFactorSecret = null;" />
                <Button label="Verify & Enable" icon="pi pi-check" :loading="confirmTwoFactorForm.processing"
                    :disabled="!confirmTwoFactorForm.code || confirmTwoFactorForm.code.length !== 6"
                    @click="confirmTwoFactor" />
            </template>
        </Dialog>

        <!-- Recovery Codes Dialog -->
        <Dialog v-model:visible="showRecoveryCodesDialog" header="Recovery Codes" :modal="true"
            :style="{ width: '500px' }">
            <div class="space-y-4">
                <div
                    class="p-4 border border-yellow-200 rounded-lg bg-yellow-50 dark:bg-yellow-900/20 dark:border-yellow-800">
                    <div class="flex items-start gap-3">
                        <i class="pi pi-exclamation-triangle text-yellow-500 mt-0.5"></i>
                        <div class="text-sm text-yellow-700 dark:text-yellow-300">
                            <p class="mb-1 font-medium">Important: Save these recovery codes!</p>
                            <p class="text-yellow-600 dark:text-yellow-400">
                                Store these codes in a safe place. They can be used to access your account if you lose
                                access to
                                your authenticator app.
                                Each code can only be used once.
                            </p>
                        </div>
                    </div>
                </div>

                <!-- Recovery Codes Grid -->
                <div
                    class="grid grid-cols-2 gap-2 p-4 border border-gray-200 rounded-lg bg-gray-50 dark:bg-gray-700/50 dark:border-gray-600">
                    <div v-for="(code, index) in recoveryCodes" :key="index"
                        class="p-2 font-mono text-sm text-center bg-white border border-gray-200 rounded dark:bg-gray-800 dark:border-gray-600">
                        {{ code }}
                    </div>
                </div>

                <!-- Action Buttons -->
                <div class="flex gap-2">
                    <Button label="Copy Codes" icon="pi pi-copy" severity="secondary" class="flex-1"
                        @click="copyRecoveryCodes" />
                    <Button label="Download" icon="pi pi-download" severity="secondary" class="flex-1"
                        @click="downloadRecoveryCodes" />
                </div>
            </div>

            <template #footer>
                <Button label="I've Saved My Codes" icon="pi pi-check" @click="showRecoveryCodesDialog = false" />
            </template>
        </Dialog>

        <!-- Disable Two-Factor Dialog -->
        <Dialog v-model:visible="showDisableTwoFactorDialog" header="Disable Two-Factor Authentication" :modal="true"
            :style="{ width: '450px' }">
            <div class="mb-4 text-center">
                <div
                    class="flex items-center justify-center w-16 h-16 mx-auto mb-4 bg-red-100 rounded-full dark:bg-red-900/50">
                    <i class="text-3xl text-red-500 pi pi-shield"></i>
                </div>
                <p class="text-gray-600 dark:text-gray-300">
                    Are you sure you want to disable two-factor authentication? This will make your account less secure.
                </p>
            </div>

            <div class="mb-4">
                <label class="block mb-2 text-sm font-medium text-gray-700 dark:text-gray-300">
                    Enter your password to confirm
                </label>
                <Password v-model="disableTwoFactorForm.password" class="w-full" :feedback="false" toggleMask
                    inputClass="w-full" placeholder="Enter your password" />
                <small v-if="disableTwoFactorForm.errors.password" class="text-red-500"> {{
                    disableTwoFactorForm.errors.password }}
                </small>
            </div>

            <template #footer>
                <Button label="Cancel" severity="secondary" @click="showDisableTwoFactorDialog = false" />
                <Button label="Disable 2FA" severity="danger" :loading="disableTwoFactorForm.processing"
                    @click="disableTwoFactor" />
            </template>
        </Dialog>
    </DashboardLayout>
</template>

<style scoped>
:deep(.p-password input) {
    width: 100%;
}

.qr-code :deep(svg) {
    width: 200px;
    height: 200px;
}
</style>
