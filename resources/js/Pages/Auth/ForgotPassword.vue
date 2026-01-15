<script setup>
import { computed } from 'vue';
import { Head, Link, useForm, usePage } from '@inertiajs/vue3';
import InputText from 'primevue/inputtext';
import Button from 'primevue/button';
import Message from 'primevue/message';

defineProps({
    status: {
        type: String,
    },
});

const page = usePage();
const settings = computed(() => page.props.settings || {});

const form = useForm({
    email: '',
});

const submit = () => {
    form.post(route('password.email'));
};
</script>

<template>

    <Head title="Forgot Password" />

    <div class="flex items-center justify-center min-h-screen p-6 bg-gray-50 dark:bg-zinc-900">
        <div class="w-full max-w-md">
            <!-- Logo -->
            <div class="mb-8 text-center">
                <Link href="/" class="inline-block">
                <img v-if="settings.site_logo" :src="settings.site_logo" :alt="settings.site_name || 'Logo'"
                    class="h-12 max-w-[220px] object-contain mx-auto dark:hidden" />
                <img v-if="settings.site_logo_dark" :src="settings.site_logo_dark" :alt="settings.site_name || 'Logo'"
                    class="h-12 max-w-[220px] object-contain mx-auto hidden dark:block" />
                <h1 v-if="!settings.site_logo" class="text-2xl font-bold text-gray-900 dark:text-white">
                    {{ settings.site_name || 'NationalResourceBenefits' }}
                </h1>
                </Link>
            </div>

            <!-- Card -->
            <div
                class="p-8 bg-white border border-gray-100 shadow-xl dark:bg-zinc-800 rounded-2xl dark:border-zinc-700">
                <!-- Header -->
                <div class="mb-6 text-center">
                    <div
                        class="flex items-center justify-center w-16 h-16 mx-auto mb-4 bg-blue-100 rounded-full dark:bg-blue-900/30">
                        <i class="text-2xl text-blue-600 pi pi-lock dark:text-blue-400"></i>
                    </div>
                    <h2 class="mb-2 text-2xl font-bold text-gray-900 dark:text-white">
                        Forgot Password?
                    </h2>
                    <p class="text-sm text-gray-600 dark:text-gray-400">
                        No problem. Enter your email address and we'll send you a link to reset your password.
                    </p>
                </div>

                <!-- Status Message -->
                <Message v-if="status" severity="success" :closable="false" class="mb-6">
                    {{ status }}
                </Message>

                <!-- Error Message -->
                <Message v-if="form.errors.email" severity="error" :closable="true" class="mb-6">
                    {{ form.errors.email }}
                </Message>

                <!-- Form -->
                <form @submit.prevent="submit" class="space-y-5">
                    <!-- Email Field -->
                    <div>
                        <label for="email" class="block mb-2 text-sm font-medium text-gray-700 dark:text-gray-300">
                            Email Address
                        </label>
                        <div class="relative">
                            <i class="absolute text-gray-400 -translate-y-1/2 pi pi-envelope left-4 top-1/2"></i>
                            <InputText id="email" v-model="form.email" type="email" class="w-full input-with-icon"
                                :class="{ 'p-invalid': form.errors.email }" placeholder="Enter your email address"
                                required autofocus autocomplete="email" />
                        </div>
                    </div>

                    <!-- Submit Button -->
                    <Button type="submit" :loading="form.processing" :disabled="form.processing"
                        class="justify-center w-full py-3 text-base font-semibold" severity="success">
                        <i class="mr-2 pi pi-send"></i>
                        Send Reset Link
                    </Button>
                </form>

                <!-- Back to Login -->
                <div class="mt-6 text-center">
                    <Link :href="route('login')"
                        class="inline-flex items-center gap-2 text-sm text-gray-600 dark:text-gray-400 hover:text-gray-900 dark:hover:text-white">
                    <i class="text-xs pi pi-arrow-left"></i>
                    Back to Sign In
                    </Link>
                </div>
            </div>

            <!-- Footer Links -->
            <div class="flex items-center justify-center gap-4 mt-8 text-xs text-gray-500 dark:text-gray-400">
                <a href="#" class="hover:text-gray-700 dark:hover:text-gray-300">Privacy Policy</a>
                <span>•</span>
                <a href="#" class="hover:text-gray-700 dark:hover:text-gray-300">Terms of Service</a>
                <span>•</span>
                <a href="#" class="hover:text-gray-700 dark:hover:text-gray-300">Contact Support</a>
            </div>
        </div>
    </div>
</template>

<style scoped>
:deep(.p-inputtext) {
    @apply bg-white dark:bg-zinc-800 border-gray-300 dark:border-zinc-600 rounded-xl py-3 px-4;
}

:deep(.p-inputtext.input-with-icon) {
    padding-left: 2.75rem !important;
}

:deep(.p-inputtext:focus) {
    @apply ring-2 ring-green-500 border-green-500;
}

:deep(.p-inputtext.p-invalid) {
    @apply border-red-500;
}

:deep(.p-button.p-button-success) {
    @apply bg-green-600 hover:bg-green-700 border-green-600 hover:border-green-700 rounded-xl;
}
</style>
