<script setup>
import { Head, Link, usePage } from '@inertiajs/vue3';
import { ref, computed } from 'vue';

defineProps({
    title: {
        type: String,
        default: ''
    }
});

// Get settings from page props
const page = usePage();
const settings = computed(() => page.props.settings || {});
const siteName = computed(() => settings.value.site_name || 'NationalResourceBenefits.gov');
const siteLogo = computed(() => settings.value.site_logo);

const mobileMenuOpen = ref(false);
const showGovBanner = ref(false);

// Navigation menu items
const navItems = [
    { label: 'Home', href: '/' },
    { label: 'About', href: '/about' },
    { label: 'Programs', href: '/#apply-section' },
    { label: 'FAQ', href: '/faq' },
    { label: 'Contact', href: '/#contact' },
];
</script>

<template>

    <Head :title="title ? `${title} - ${siteName}` : siteName" />

    <div class="min-h-screen bg-gray-50 text-black/50">
        <!-- Official Government Banner -->
        <div class="bg-gray-100 border-b border-gray-200">
            <div class="px-4 py-2 mx-auto max-w-7xl sm:px-6 lg:px-8">
                <div class="flex flex-wrap items-center gap-2 text-xs text-gray-600">
                    <img src="/images/us_flag_small.png" alt="U.S. flag" class="w-4 h-3" />
                    <span>An official website of the United States government</span>
                    <button @click="showGovBanner = !showGovBanner"
                        class="flex items-center gap-1 ml-1 text-blue-600 hover:underline">
                        <span>Here's how you know</span>
                        <svg class="w-3 h-3 transition-transform duration-200" :class="{ 'rotate-180': showGovBanner }"
                            fill="none" stroke="currentColor" viewBox="0 0 24 24">
                            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M19 9l-7 7-7-7" />
                        </svg>
                    </button>
                </div>
            </div>

            <!-- Expandable Banner Content -->
            <transition enter-active-class="transition duration-200 ease-out"
                enter-from-class="transform -translate-y-2 opacity-0"
                enter-to-class="transform translate-y-0 opacity-100"
                leave-active-class="transition duration-150 ease-in"
                leave-from-class="transform translate-y-0 opacity-100"
                leave-to-class="transform -translate-y-2 opacity-0">
                <div v-if="showGovBanner" class="bg-gray-100 border-t border-gray-200">
                    <div class="px-4 py-4 mx-auto max-w-7xl sm:px-6 lg:px-8">
                        <div class="grid grid-cols-1 gap-6 sm:grid-cols-2 md:gap-8">
                            <!-- Official websites column -->
                            <div class="flex gap-3">
                                <div class="flex-shrink-0">
                                    <svg class="w-10 h-10 text-[#112e51]" viewBox="0 0 64 64" fill="currentColor">
                                        <circle cx="32" cy="32" r="30" fill="none" stroke="currentColor"
                                            stroke-width="2" />
                                        <path d="M32 12 L12 26 L12 28 L52 28 L52 26 Z" />
                                        <rect x="16" y="30" width="6" height="18" />
                                        <rect x="26" y="30" width="6" height="18" />
                                        <rect x="38" y="30" width="6" height="18" />
                                        <rect x="48" y="30" width="4" height="18" />
                                        <rect x="12" y="48" width="40" height="4" />
                                    </svg>
                                </div>
                                <div>
                                    <p class="text-sm font-bold text-gray-800">Official websites use .gov</p>
                                    <p class="mt-1 text-xs leading-relaxed text-gray-600">
                                        A <strong>.gov</strong> website belongs to an official government organization
                                        in the United States.
                                    </p>
                                </div>
                            </div>

                            <!-- Secure websites column -->
                            <div class="flex gap-3">
                                <div class="flex-shrink-0">
                                    <svg class="w-10 h-10 text-[#112e51]" viewBox="0 0 52 64" fill="currentColor">
                                        <path
                                            d="M26 0c-9.9 0-18 8.1-18 18v8h-4c-2.2 0-4 1.8-4 4v30c0 2.2 1.8 4 4 4h44c2.2 0 4-1.8 4-4v-30c0-2.2-1.8-4-4-4h-4v-8c0-9.9-8.1-18-18-18zm0 8c5.5 0 10 4.5 10 10v8h-20v-8c0-5.5 4.5-10 10-10zm0 32c2.8 0 5 2.2 5 5 0 1.6-.8 3.1-2 4v5c0 1.7-1.3 3-3 3s-3-1.3-3-3v-5c-1.2-.9-2-2.4-2-4 0-2.8 2.2-5 5-5z" />
                                    </svg>
                                </div>
                                <div>
                                    <p class="text-sm font-bold text-gray-800">Secure .gov websites use HTTPS</p>
                                    <p class="mt-1 text-xs leading-relaxed text-gray-600">
                                        A <strong>lock</strong> or <strong>https://</strong> means you've safely
                                        connected to the .gov
                                        website. Share sensitive information only on official, secure websites.
                                    </p>
                                </div>
                            </div>
                        </div>
                    </div>
                </div>
            </transition>
        </div>

        <!-- Header -->
        <header class="w-full bg-[#112e51] sticky top-0 z-50 shadow-lg">
            <!-- Top Header with Logo and Auth -->
            <div class="px-4 py-4 mx-auto max-w-7xl sm:px-6 lg:px-8">
                <div class="flex items-center justify-between">
                    <!-- Logo -->
                    <Link href="/" class="flex items-center gap-3">
                    <template v-if="siteLogo">
                        <img :src="siteLogo" :alt="siteName" class="w-auto h-10 md:h-12" />
                    </template>
                    <template v-else>
                        <div class="flex items-center justify-center w-10 h-10 bg-white rounded-full md:w-12 md:h-12">
                            <svg class="w-6 h-6 md:w-8 md:h-8 text-[#112e51]" fill="currentColor" viewBox="0 0 24 24">
                                <path d="M12 2L2 7l10 5 10-5-10-5zM2 17l10 5 10-5M2 12l10 5 10-5" />
                            </svg>
                        </div>
                        <div class="text-lg font-bold text-white logo md:text-2xl">
                            NationalResource<span class="text-[#02bfe7]">Benefits</span><span
                                class="text-white">.gov</span>
                        </div>
                    </template>
                    </Link>

                    <!-- Desktop Auth Buttons -->
                    <nav class="items-center hidden gap-4 md:flex">
                        <Link v-if="$page.props.auth?.user" :href="route('dashboard')"
                            class="rounded-md px-4 py-2 bg-[#02bfe7] text-[#112e51] font-semibold transition hover:bg-[#00a6ce] text-sm">
                        Dashboard
                        </Link>
                        <template v-else>
                            <Link :href="route('login')"
                                class="px-4 py-2 text-sm font-semibold text-white transition border border-white rounded-md hover:bg-white/10">
                            Member Login
                            </Link>
                            <Link :href="route('register')"
                                class="rounded-md px-4 py-2 bg-[#02bfe7] text-[#112e51] font-semibold transition hover:bg-[#00a6ce] text-sm">
                            Get Started
                            </Link>
                        </template>
                    </nav>

                    <!-- Mobile Menu Button -->
                    <button @click="mobileMenuOpen = !mobileMenuOpen" class="p-2 text-white md:hidden">
                        <svg v-if="!mobileMenuOpen" class="w-6 h-6" fill="none" stroke="currentColor"
                            viewBox="0 0 24 24">
                            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2"
                                d="M4 6h16M4 12h16M4 18h16" />
                        </svg>
                        <svg v-else class="w-6 h-6" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2"
                                d="M6 18L18 6M6 6l12 12" />
                        </svg>
                    </button>
                </div>
            </div>

            <!-- Navigation Bar -->
            <div class="bg-[#205493] border-t border-[#112e51]/50">
                <div class="px-4 mx-auto max-w-7xl sm:px-6 lg:px-8">
                    <!-- Desktop Navigation -->
                    <nav class="items-center hidden gap-1 md:flex">
                        <a v-for="item in navItems" :key="item.label" :href="item.href"
                            class="px-4 py-3 text-white font-medium hover:bg-[#112e51] transition-colors text-sm">
                            {{ item.label }}
                        </a>
                    </nav>
                </div>
            </div>

            <!-- Mobile Menu -->
            <div v-if="mobileMenuOpen" class="md:hidden bg-[#205493] border-t border-[#112e51]/50">
                <nav class="flex flex-col">
                    <a v-for="item in navItems" :key="item.label" :href="item.href"
                        class="px-4 py-3 text-white font-medium hover:bg-[#112e51] transition-colors text-sm border-b border-[#112e51]/30">
                        {{ item.label }}
                    </a>
                    <!-- Mobile Auth Links -->
                    <div v-if="!$page.props.auth?.user" class="flex flex-col p-4 gap-2 border-t border-[#112e51]/50">
                        <Link :href="route('login')"
                            class="w-full px-4 py-2 text-sm font-semibold text-center text-white transition border border-white rounded-md hover:bg-white/10">
                        Member Login
                        </Link>
                        <Link :href="route('register')"
                            class="w-full text-center rounded-md px-4 py-2 bg-[#02bfe7] text-[#112e51] font-semibold transition hover:bg-[#00a6ce] text-sm">
                        Get Started
                        </Link>
                    </div>
                </nav>
            </div>
        </header>

        <!-- Main Content -->
        <main>
            <slot />
        </main>

        <!-- Footer -->
        <footer class="bg-[#112e51] text-white mt-10 sm:mt-16">
            <!-- Main Footer -->
            <div class="px-4 py-8 mx-auto sm:py-12 max-w-7xl sm:px-6 lg:px-8">
                <div class="grid grid-cols-1 gap-8 sm:grid-cols-2 md:grid-cols-4">
                    <!-- Logo & Description -->
                    <div class="sm:col-span-2">
                        <div class="flex items-center gap-2 mb-3 sm:gap-3 sm:mb-4">
                            <template v-if="siteLogo">
                                <img :src="siteLogo" :alt="siteName" class="w-auto h-8 sm:h-10 brightness-0 invert" />
                            </template>
                            <template v-else>
                                <div
                                    class="flex items-center justify-center w-8 h-8 bg-white rounded-full sm:w-10 sm:h-10">
                                    <svg class="w-5 h-5 sm:w-6 sm:h-6 text-[#112e51]" fill="currentColor"
                                        viewBox="0 0 24 24">
                                        <path d="M12 2L2 7l10 5 10-5-10-5zM2 17l10 5 10-5M2 12l10 5 10-5" />
                                    </svg>
                                </div>
                                <div class="text-lg font-bold sm:text-xl">
                                    NationalResource<span class="text-[#02bfe7]">Benefits</span>.gov
                                </div>
                            </template>
                        </div>
                        <p class="text-xs leading-relaxed text-gray-300 sm:text-sm">
                            A secure gateway ensuring federal funds reach the intended recipients, preventing improper
                            payments and reducing fraud, waste, and abuse.
                        </p>

                        <!-- Social Media Links -->
                        <div v-if="settings.social" class="flex gap-3 mt-4">
                            <a v-if="settings.social.facebook" :href="settings.social.facebook" target="_blank"
                                rel="noopener noreferrer" class="text-gray-400 transition hover:text-white"
                                title="Facebook">
                                <svg class="w-5 h-5" fill="currentColor" viewBox="0 0 24 24">
                                    <path
                                        d="M24 12.073c0-6.627-5.373-12-12-12s-12 5.373-12 12c0 5.99 4.388 10.954 10.125 11.854v-8.385H7.078v-3.47h3.047V9.43c0-3.007 1.792-4.669 4.533-4.669 1.312 0 2.686.235 2.686.235v2.953H15.83c-1.491 0-1.956.925-1.956 1.874v2.25h3.328l-.532 3.47h-2.796v8.385C19.612 23.027 24 18.062 24 12.073z" />
                                </svg>
                            </a>
                            <a v-if="settings.social.twitter" :href="settings.social.twitter" target="_blank"
                                rel="noopener noreferrer" class="text-gray-400 transition hover:text-white"
                                title="Twitter/X">
                                <svg class="w-5 h-5" fill="currentColor" viewBox="0 0 24 24">
                                    <path
                                        d="M18.244 2.25h3.308l-7.227 8.26 8.502 11.24H16.17l-5.214-6.817L4.99 21.75H1.68l7.73-8.835L1.254 2.25H8.08l4.713 6.231zm-1.161 17.52h1.833L7.084 4.126H5.117z" />
                                </svg>
                            </a>
                            <a v-if="settings.social.instagram" :href="settings.social.instagram" target="_blank"
                                rel="noopener noreferrer" class="text-gray-400 transition hover:text-white"
                                title="Instagram">
                                <svg class="w-5 h-5" fill="currentColor" viewBox="0 0 24 24">
                                    <path
                                        d="M12.315 2c2.43 0 2.784.013 3.808.06 1.064.049 1.791.218 2.427.465a4.902 4.902 0 011.772 1.153 4.902 4.902 0 011.153 1.772c.247.636.416 1.363.465 2.427.048 1.067.06 1.407.06 4.123v.08c0 2.643-.012 2.987-.06 4.043-.049 1.064-.218 1.791-.465 2.427a4.902 4.902 0 01-1.153 1.772 4.902 4.902 0 01-1.772 1.153c-.636.247-1.363.416-2.427.465-1.067.048-1.407.06-4.123.06h-.08c-2.643 0-2.987-.012-4.043-.06-1.064-.049-1.791-.218-2.427-.465a4.902 4.902 0 01-1.772-1.153 4.902 4.902 0 01-1.153-1.772c-.247-.636-.416-1.363-.465-2.427-.047-1.024-.06-1.379-.06-3.808v-.63c0-2.43.013-2.784.06-3.808.049-1.064.218-1.791.465-2.427a4.902 4.902 0 011.153-1.772A4.902 4.902 0 015.45 2.525c.636-.247 1.363-.416 2.427-.465C8.901 2.013 9.256 2 11.685 2h.63zm-.081 1.802h-.468c-2.456 0-2.784.011-3.807.058-.975.045-1.504.207-1.857.344-.467.182-.8.398-1.15.748-.35.35-.566.683-.748 1.15-.137.353-.3.882-.344 1.857-.047 1.023-.058 1.351-.058 3.807v.468c0 2.456.011 2.784.058 3.807.045.975.207 1.504.344 1.857.182.466.399.8.748 1.15.35.35.683.566 1.15.748.353.137.882.3 1.857.344 1.054.048 1.37.058 4.041.058h.08c2.597 0 2.917-.01 3.96-.058.976-.045 1.505-.207 1.858-.344.466-.182.8-.398 1.15-.748.35-.35.566-.683.748-1.15.137-.353.3-.882.344-1.857.048-1.055.058-1.37.058-4.041v-.08c0-2.597-.01-2.917-.058-3.96-.045-.976-.207-1.505-.344-1.858a3.097 3.097 0 00-.748-1.15 3.098 3.098 0 00-1.15-.748c-.353-.137-.882-.3-1.857-.344-1.023-.047-1.351-.058-3.807-.058zM12 6.865a5.135 5.135 0 110 10.27 5.135 5.135 0 010-10.27zm0 1.802a3.333 3.333 0 100 6.666 3.333 3.333 0 000-6.666zm5.338-3.205a1.2 1.2 0 110 2.4 1.2 1.2 0 010-2.4z" />
                                </svg>
                            </a>
                            <a v-if="settings.social.linkedin" :href="settings.social.linkedin" target="_blank"
                                rel="noopener noreferrer" class="text-gray-400 transition hover:text-white"
                                title="LinkedIn">
                                <svg class="w-5 h-5" fill="currentColor" viewBox="0 0 24 24">
                                    <path
                                        d="M20.447 20.452h-3.554v-5.569c0-1.328-.027-3.037-1.852-3.037-1.853 0-2.136 1.445-2.136 2.939v5.667H9.351V9h3.414v1.561h.046c.477-.9 1.637-1.85 3.37-1.85 3.601 0 4.267 2.37 4.267 5.455v6.286zM5.337 7.433c-1.144 0-2.063-.926-2.063-2.065 0-1.138.92-2.063 2.063-2.063 1.14 0 2.064.925 2.064 2.063 0 1.139-.925 2.065-2.064 2.065zm1.782 13.019H3.555V9h3.564v11.452zM22.225 0H1.771C.792 0 0 .774 0 1.729v20.542C0 23.227.792 24 1.771 24h20.451C23.2 24 24 23.227 24 22.271V1.729C24 .774 23.2 0 22.222 0h.003z" />
                                </svg>
                            </a>
                            <a v-if="settings.social.youtube" :href="settings.social.youtube" target="_blank"
                                rel="noopener noreferrer" class="text-gray-400 transition hover:text-white"
                                title="YouTube">
                                <svg class="w-5 h-5" fill="currentColor" viewBox="0 0 24 24">
                                    <path
                                        d="M23.498 6.186a3.016 3.016 0 0 0-2.122-2.136C19.505 3.545 12 3.545 12 3.545s-7.505 0-9.377.505A3.017 3.017 0 0 0 .502 6.186C0 8.07 0 12 0 12s0 3.93.502 5.814a3.016 3.016 0 0 0 2.122 2.136c1.871.505 9.376.505 9.376.505s7.505 0 9.377-.505a3.015 3.015 0 0 0 2.122-2.136C24 15.93 24 12 24 12s0-3.93-.502-5.814zM9.545 15.568V8.432L15.818 12l-6.273 3.568z" />
                                </svg>
                            </a>
                        </div>
                    </div>

                    <!-- Quick Links -->
                    <div>
                        <h4 class="font-bold text-[#02bfe7] mb-3 sm:mb-4 text-sm sm:text-base">Quick Links</h4>
                        <ul class="space-y-1 text-xs sm:space-y-2 sm:text-sm">
                            <li><a href="/about" class="text-gray-300 transition hover:text-white">About Us</a></li>
                            <li><a href="/#apply-section" class="text-gray-300 transition hover:text-white">Programs</a>
                            </li>
                            <li><a href="/faq" class="text-gray-300 transition hover:text-white">FAQ</a></li>
                            <li><a href="/#contact" class="text-gray-300 transition hover:text-white">Contact</a></li>
                        </ul>
                    </div>

                    <!-- Legal Links -->
                    <div>
                        <h4 class="font-bold text-[#02bfe7] mb-3 sm:mb-4 text-sm sm:text-base">Legal</h4>
                        <ul class="space-y-1 text-xs sm:space-y-2 sm:text-sm">
                            <li><a href="#" class="text-gray-300 transition hover:text-white">Privacy Policy</a></li>
                            <li><a href="#" class="text-gray-300 transition hover:text-white">Terms of Service</a></li>
                            <li><a href="#" class="text-gray-300 transition hover:text-white">Accessibility</a></li>
                            <li><a href="#" class="text-gray-300 transition hover:text-white">FOIA Requests</a></li>
                        </ul>
                    </div>
                </div>
            </div>

            <!-- Bottom Bar -->
            <div class="border-t border-white/10">
                <div class="px-4 py-3 mx-auto sm:py-4 max-w-7xl sm:px-6 lg:px-8">
                    <div
                        class="flex flex-col items-center justify-between gap-3 text-xs text-center text-gray-400 sm:text-sm sm:gap-4 md:flex-row md:text-left">
                        <div>© {{ new Date().getFullYear() }} {{ siteName }} - All Rights Reserved</div>
                        <div class="flex flex-wrap items-center justify-center gap-3 sm:gap-4">
                            <a href="#" class="transition hover:text-white">USA.gov</a>
                            <a href="#" class="transition hover:text-white">WhiteHouse.gov</a>
                            <a href="#" class="transition hover:text-white">Report Fraud</a>
                        </div>
                    </div>
                </div>
            </div>
        </footer>
    </div>
</template>
