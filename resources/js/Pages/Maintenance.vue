<script setup>
import { Head } from '@inertiajs/vue3';

defineProps({
    message: {
        type: String,
        default: 'We are currently performing scheduled maintenance. Please check back soon.'
    }
});
</script>

<template>

    <Head title="Maintenance - Site Under Maintenance" />

    <div class="flex flex-col items-center justify-center min-h-screen px-4 bg-gray-100">
        <div class="w-full max-w-lg p-8 text-center bg-white rounded-lg shadow-lg">
            <!-- Maintenance Icon -->
            <div
                class="flex items-center justify-center w-20 h-20 mx-auto mb-6 text-yellow-600 bg-yellow-100 rounded-full">
                <svg class="w-10 h-10" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                    <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2"
                        d="M12 9v2m0 4h.01m-6.938 4h13.856c1.54 0 2.502-1.667 1.732-3L13.732 4c-.77-1.333-2.694-1.333-3.464 0L3.34 16c-.77 1.333.192 3 1.732 3z" />
                </svg>
            </div>

            <!-- Title -->
            <h1 class="mb-4 text-2xl font-bold text-gray-900 md:text-3xl">
                Site Under Maintenance
            </h1>

            <!-- Message -->
            <p class="mb-6 text-gray-600">
                {{ message }}
            </p>

            <!-- Progress indicator -->
            <div class="w-full h-2 overflow-hidden bg-gray-200 rounded-full">
                <div class="h-full bg-yellow-500 rounded-full animate-pulse" style="width: 60%"></div>
            </div>

            <p class="mt-4 text-sm text-gray-500">
                We apologize for any inconvenience. Thank you for your patience.
            </p>

            <!-- Contact Info -->
            <div class="pt-6 mt-6 border-t border-gray-200">
                <p class="text-sm text-gray-500">
                    If you need immediate assistance, please contact us at
                    <a href="mailto:support@nationalresourcebenefits.gov" class="text-blue-600 hover:underline">
                        support@nationalresourcebenefits.us
                    </a>
                </p>
            </div>
        </div>

        <!-- Footer -->
        <p class="mt-8 text-sm text-gray-500">
            © {{ new Date().getFullYear() }} NationalResourceBenefits.gov
        </p>
    </div>
</template>
