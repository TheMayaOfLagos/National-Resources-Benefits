<script setup>
import { ref, computed } from 'vue';
import { Head, Link, useForm, usePage } from '@inertiajs/vue3';
import InputText from 'primevue/inputtext';
import Button from 'primevue/button';
import Message from 'primevue/message';

const page = usePage();
const settings = computed(() => page.props.settings || {});

const form = useForm({
    password: '',
});

const showPassword = ref(false);

const submit = () => {
    form.post(route('password.confirm'), {
        onFinish: () => form.reset(),
    });
};
</script>

<template>

    <Head title="Confirm Password" />

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
                        class="flex items-center justify-center w-16 h-16 mx-auto mb-4 rounded-full bg-amber-100 dark:bg-amber-900/30">
                        <i class="text-2xl pi pi-shield text-amber-600 dark:text-amber-400"></i>
                    </div>
                    <h2 class="mb-2 text-2xl font-bold text-gray-900 dark:text-white">
                        Confirm Password
                    </h2>
                    <p class="text-sm text-gray-600 dark:text-gray-400">
                        This is a secure area. Please confirm your password before continuing.
                    </p>
                </div>

                <!-- Error Message -->
                <Message v-if="form.errors.password" severity="error" :closable="true" class="mb-6">
                    {{ form.errors.password }}
                </Message>

                <!-- Form -->
                <form @submit.prevent="submit" class="space-y-5">
                    <!-- Password Field -->
                    <div>
                        <label for="password" class="block mb-2 text-sm font-medium text-gray-700 dark:text-gray-300">
                            Password
                        </label>
                        <div class="relative">
                            <i class="absolute text-gray-400 -translate-y-1/2 pi pi-lock left-4 top-1/2"></i>
                            <InputText id="password" v-model="form.password" :type="showPassword ? 'text' : 'password'"
                                class="w-full input-with-icon-both" :class="{ 'p-invalid': form.errors.password }"
                                placeholder="Enter your password" required autofocus autocomplete="current-password" />
                            <button type="button" @click="showPassword = !showPassword"
                                class="absolute text-gray-400 -translate-y-1/2 right-4 top-1/2 hover:text-gray-600 dark:hover:text-gray-300">
                                <i :class="showPassword ? 'pi pi-eye-slash' : 'pi pi-eye'"></i>
                            </button>
                        </div>
                    </div>

                    <!-- Submit Button -->
                    <Button type="submit" :loading="form.processing" :disabled="form.processing"
                        class="justify-center w-full py-3 text-base font-semibold" severity="success">
                        <i class="mr-2 pi pi-check"></i>
                        Confirm
                    </Button>
                </form>
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

:deep(.p-inputtext.input-with-icon-both) {
    padding-left: 2.75rem !important;
    padding-right: 2.75rem !important;
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
