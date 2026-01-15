<script setup>
import { Head, Link, router, usePage } from '@inertiajs/vue3';
import { ref, onMounted, onUnmounted, computed } from 'vue';

defineProps({
    canLogin: {
        type: Boolean,
    },
    canRegister: {
        type: Boolean,
    },
    laravelVersion: {
        type: String,
        required: true,
    },
    phpVersion: {
        type: String,
        required: true,
    },
});

// Get settings from page props
const page = usePage();
const settings = computed(() => page.props.settings || {});
const siteName = computed(() => settings.value.site_name || 'NationalResourceBenefits.gov');
const siteLogo = computed(() => settings.value.site_logo);
const siteLogoDark = computed(() => settings.value.site_logo_dark || settings.value.site_logo);

const mobileMenuOpen = ref(false);
const showGovBanner = ref(false);

// How it works steps
const howItWorksSteps = [
    {
        title: 'Register',
        description: 'Easy to fill out. Your information is secure and protected.',
        icon: 'clipboard'
    },
    {
        title: 'Research',
        description: 'We provide the application sources and teach you how to apply.',
        icon: 'search'
    },
    {
        title: 'Apply',
        description: "There's no limit to the number of funding sources you can apply to.",
        icon: 'check'
    }
];

// Testimonials - What people are saying
const testimonials = [
    {
        quote: 'It is simple and easy to use. The wording is great and understandable.',
        name: 'Karen R.'
    },
    {
        quote: 'I especially like the fact that there are a variety of fields to choose from which allows individuals to pursue their dreams and stay encouraged.',
        name: 'Leslie A.'
    },
    {
        quote: 'Hey, I recommend everyone and everybody to these services because you can get whatever you need to know about personal assistance, food & nutrition.',
        name: 'Lunice S.'
    },
    {
        quote: 'It was so thorough. Not only did it categorize the funding programs but it had tips for the entire process.',
        name: 'Roxanne J.'
    },
    {
        quote: 'It was easy to understand, it didn\'t seem like a hassle at all to answer anything that was asked.',
        name: 'Sandra G.'
    },
    {
        quote: 'I think this funding application is a great way to give the less fortunate a chance to live a better life.',
        name: 'Bobbie G.'
    }
];

// Testimonial carousel state
const currentTestimonialIndex = ref(0);
let testimonialInterval = null;

const nextTestimonial = () => {
    currentTestimonialIndex.value = (currentTestimonialIndex.value + 1) % testimonials.length;
};

const prevTestimonial = () => {
    currentTestimonialIndex.value = (currentTestimonialIndex.value - 1 + testimonials.length) % testimonials.length;
};

const goToTestimonial = (index) => {
    currentTestimonialIndex.value = index;
};

const startAutoSlide = () => {
    testimonialInterval = setInterval(() => {
        nextTestimonial();
    }, 5000); // Auto-slide every 5 seconds
};

const stopAutoSlide = () => {
    if (testimonialInterval) {
        clearInterval(testimonialInterval);
    }
};

onMounted(() => {
    startAutoSlide();
});

onUnmounted(() => {
    stopAutoSlide();
});

// Navigation menu items
const navItems = [
    { label: 'Home', href: '/' },
    { label: 'About', href: '/about' },
    { label: 'Resources', href: '#how-it-works' },
    { label: 'FAQ', href: '/faq' },
];
</script>

<template>

    <Head :title="`Welcome - ${siteName}`" />

    <div class="min-h-screen bg-gray-50 text-black/50 dark:bg-zinc-900 dark:text-white/50">
        <!-- Official Government Banner -->
        <div class="bg-gray-100 border-b border-gray-200 dark:bg-zinc-800 dark:border-zinc-700">
            <div class="px-4 py-2 mx-auto max-w-7xl sm:px-6 lg:px-8">
                <div class="flex flex-wrap items-center gap-2 text-xs text-gray-600 dark:text-gray-400">
                    <img src="/images/us_flag_small.png" alt="U.S. flag" class="w-4 h-3" />
                    <span>An official website of the United States government</span>
                    <button @click="showGovBanner = !showGovBanner"
                        class="flex items-center gap-1 ml-1 text-blue-600 dark:text-blue-400 hover:underline">
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
                <div v-if="showGovBanner"
                    class="bg-gray-100 border-t border-gray-200 dark:bg-zinc-800 dark:border-zinc-700">
                    <div class="px-4 py-4 mx-auto max-w-7xl sm:px-6 lg:px-8">
                        <div class="grid grid-cols-1 gap-6 sm:grid-cols-2 md:gap-8">
                            <!-- Official websites column -->
                            <div class="flex gap-3">
                                <div class="flex-shrink-0">
                                    <!-- Government Building Icon -->
                                    <svg class="w-10 h-10 text-[#112e51] dark:text-blue-400" viewBox="0 0 64 64"
                                        fill="currentColor">
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
                                    <p class="text-sm font-bold text-gray-800 dark:text-white">
                                        Official websites use .gov
                                    </p>
                                    <p class="mt-1 text-xs leading-relaxed text-gray-600 dark:text-gray-400">
                                        A <strong>.gov</strong> website belongs to an official government organization
                                        in the United States.
                                    </p>
                                </div>
                            </div>

                            <!-- Secure websites column -->
                            <div class="flex gap-3">
                                <div class="flex-shrink-0">
                                    <svg class="w-10 h-10 text-[#112e51] dark:text-blue-400"
                                        xmlns="http://www.w3.org/2000/svg" viewBox="0 0 52 64" fill="currentColor">
                                        <path
                                            d="M26 0c-9.9 0-18 8.1-18 18v8h-4c-2.2 0-4 1.8-4 4v30c0 2.2 1.8 4 4 4h44c2.2 0 4-1.8 4-4v-30c0-2.2-1.8-4-4-4h-4v-8c0-9.9-8.1-18-18-18zm0 8c5.5 0 10 4.5 10 10v8h-20v-8c0-5.5 4.5-10 10-10zm0 32c2.8 0 5 2.2 5 5 0 1.6-.8 3.1-2 4v5c0 1.7-1.3 3-3 3s-3-1.3-3-3v-5c-1.2-.9-2-2.4-2-4 0-2.8 2.2-5 5-5z" />
                                    </svg>
                                </div>
                                <div>
                                    <p class="text-sm font-bold text-gray-800 dark:text-white">
                                        Secure .gov websites use HTTPS
                                    </p>
                                    <p class="mt-1 text-xs leading-relaxed text-gray-600 dark:text-gray-400">
                                        A <strong>lock</strong> (<svg class="inline w-3 h-3" fill="currentColor"
                                            viewBox="0 0 52 64">
                                            <path
                                                d="M26 0c-9.9 0-18 8.1-18 18v8h-4c-2.2 0-4 1.8-4 4v30c0 2.2 1.8 4 4 4h44c2.2 0 4-1.8 4-4v-30c0-2.2-1.8-4-4-4h-4v-8c0-9.9-8.1-18-18-18zm0 8c5.5 0 10 4.5 10 10v8h-20v-8c0-5.5 4.5-10 10-10z" />
                                        </svg>) or <strong>https://</strong> means you've safely connected to the .gov
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
        <header class="w-full bg-[#112e51] dark:bg-zinc-900 sticky top-0 z-50 shadow-lg">
            <!-- Top Header with Logo and Auth -->
            <div class="px-4 py-4 mx-auto max-w-7xl sm:px-6 lg:px-8">
                <div class="flex items-center justify-between">
                    <!-- Logo -->
                    <div class="flex items-center gap-3">
                        <!-- Use admin-configured logo if available -->
                        <template v-if="siteLogo">
                            <img :src="siteLogo" :alt="siteName" class="w-auto h-10 md:h-12" />
                        </template>
                        <template v-else>
                            <!-- Default logo if not configured -->
                            <div
                                class="flex items-center justify-center w-10 h-10 bg-white rounded-full md:w-12 md:h-12">
                                <svg class="w-6 h-6 md:w-8 md:h-8 text-[#112e51]" fill="currentColor"
                                    viewBox="0 0 24 24">
                                    <path d="M12 2L2 7l10 5 10-5-10-5zM2 17l10 5 10-5M2 12l10 5 10-5" />
                                </svg>
                            </div>
                            <div class="text-lg font-bold text-white logo md:text-2xl">
                                NationalResource<span class="text-[#02bfe7]">Benefits</span><span
                                    class="text-white">.gov</span>
                            </div>
                        </template>
                    </div>

                    <!-- Desktop Auth Buttons -->
                    <nav v-if="canLogin" class="items-center hidden gap-4 md:flex">
                        <Link v-if="$page.props.auth.user" :href="route('dashboard')"
                            class="rounded-md px-4 py-2 bg-[#02bfe7] text-[#112e51] font-semibold transition hover:bg-[#00a6ce] text-sm">
                        Dashboard
                        </Link>

                        <template v-else>
                            <Link :href="route('login')"
                                class="px-4 py-2 text-sm font-semibold text-white transition border border-white rounded-md hover:bg-white/10">
                            Member Login
                            </Link>

                            <Link v-if="canRegister" :href="route('register')"
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
            <div class="bg-[#205493] dark:bg-zinc-800 border-t border-[#112e51]/50">
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
                    <div v-if="canLogin && !$page.props.auth.user"
                        class="flex flex-col p-4 gap-2 border-t border-[#112e51]/50">
                        <Link :href="route('login')"
                            class="w-full px-4 py-2 text-sm font-semibold text-center text-white transition border border-white rounded-md hover:bg-white/10">
                        Member Login
                        </Link>
                        <Link v-if="canRegister" :href="route('register')"
                            class="w-full text-center rounded-md px-4 py-2 bg-[#02bfe7] text-[#112e51] font-semibold transition hover:bg-[#00a6ce] text-sm">
                        Get Started
                        </Link>
                    </div>
                </nav>
            </div>
        </header>

        <!-- Hero Banner Section -->
        <section class="relative overflow-hidden">
            <!-- Background Image -->
            <div class="absolute inset-0 bg-center bg-no-repeat bg-cover"
                style="background-image: url('/images/white-house-hero.jpg');">
            </div>

            <!-- Dark Overlay -->
            <div class="absolute inset-0 bg-[#112e51]/75"></div>

            <div
                class="relative flex items-center justify-center min-h-[350px] sm:min-h-[400px] md:min-h-[500px] lg:min-h-[550px] px-4 py-16 mx-auto max-w-7xl sm:px-6 lg:px-8 md:py-28 lg:py-36">
                <div class="w-full max-w-4xl mx-auto text-center">
                    <!-- Main Title -->
                    <h1
                        class="mb-4 text-2xl font-bold leading-tight text-white sm:text-3xl sm:mb-6 md:text-5xl lg:text-6xl">
                        {{ siteName }}
                    </h1>

                    <!-- Description -->
                    <p
                        class="max-w-3xl mx-auto text-base leading-relaxed text-gray-200 sm:text-lg md:text-xl lg:text-2xl">
                        A secure gateway ensuring federal funds generated from sanctioned oil reach the intended
                        recipients,
                        preventing improper payments and reducing fraud, waste, and abuse.
                    </p>
                </div>
            </div>
        </section>

        <!-- Introduction Section (like PaymentAccuracy.gov) -->
        <section class="py-10 bg-white sm:py-12 dark:bg-zinc-800 md:py-16">
            <div class="grid-container">
                <div class="max-w-4xl mx-auto text-center">
                    <p
                        class="text-base leading-relaxed text-gray-700 sm:text-lg md:text-xl lg:text-2xl dark:text-gray-300">
                        Effective stewardship of sanctioned oils and resource funds is a critical responsibility of the
                        Federal
                        Government.
                    </p>
                    <p
                        class="mt-4 text-sm leading-relaxed text-gray-600 sm:mt-6 sm:text-base md:text-lg dark:text-gray-400">
                        This site is a window into Federal funding, paying out benefits from the resource funds, and
                        progress in
                        the prevention and recovery of improper payments, while ensuring the right individuals and
                        communities
                        benefit from federal funds.
                    </p>
                </div>
            </div>
        </section>

        <!-- How It Works Section -->
        <section
            class="py-10 border-t border-gray-200 sm:py-12 bg-gray-50 dark:bg-zinc-900 md:py-16 dark:border-zinc-700">
            <div class="grid-container">
                <h2
                    class="mb-8 text-xl font-bold text-center text-gray-900 sm:mb-12 sm:text-2xl md:text-3xl dark:text-white">
                    Here's how it works
                </h2>
                <div class="grid max-w-4xl grid-cols-1 gap-6 mx-auto sm:gap-8 sm:grid-cols-3">
                    <div v-for="(step, index) in howItWorksSteps" :key="index" class="text-center">
                        <div
                            class="w-16 h-16 mx-auto mb-3 sm:w-20 sm:h-20 sm:mb-4 flex items-center justify-center rounded-full bg-[#112e51] text-white">
                            <svg v-if="step.icon === 'clipboard'" class="w-8 h-8 sm:w-10 sm:h-10" fill="none"
                                stroke="currentColor" viewBox="0 0 24 24">
                                <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2"
                                    d="M9 5H7a2 2 0 00-2 2v12a2 2 0 002 2h10a2 2 0 002-2V7a2 2 0 00-2-2h-2M9 5a2 2 0 002 2h2a2 2 0 002-2M9 5a2 2 0 012-2h2a2 2 0 012 2m-3 7h3m-3 4h3m-6-4h.01M9 16h.01" />
                            </svg>
                            <svg v-else-if="step.icon === 'search'" class="w-8 h-8 sm:w-10 sm:h-10" fill="none"
                                stroke="currentColor" viewBox="0 0 24 24">
                                <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2"
                                    d="M21 21l-6-6m2-5a7 7 0 11-14 0 7 7 0 0114 0z" />
                            </svg>
                            <svg v-else-if="step.icon === 'check'" class="w-8 h-8 sm:w-10 sm:h-10" fill="none"
                                stroke="currentColor" viewBox="0 0 24 24">
                                <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2"
                                    d="M9 12l2 2 4-4m6 2a9 9 0 11-18 0 9 9 0 0118 0z" />
                            </svg>
                        </div>
                        <h3 class="mb-2 text-lg font-bold text-gray-900 sm:text-xl dark:text-white">{{ step.title }}
                        </h3>
                        <p class="text-sm text-gray-600 sm:text-base dark:text-gray-400">{{ step.description }}</p>
                    </div>
                </div>
            </div>
        </section>

        <!-- What People Are Saying Section -->
        <section
            class="py-10 bg-white border-t border-gray-200 sm:py-12 dark:bg-zinc-800 md:py-16 dark:border-zinc-700">
            <div class="grid-container">
                <h2
                    class="mb-8 text-xl font-bold text-center text-gray-900 sm:text-2xl sm:mb-12 md:text-3xl dark:text-white">
                    What people are saying:
                </h2>

                <!-- Carousel Container -->
                <div class="relative max-w-3xl px-2 mx-auto sm:px-0" @mouseenter="stopAutoSlide"
                    @mouseleave="startAutoSlide">
                    <!-- Testimonial Slides -->
                    <div class="overflow-hidden">
                        <div class="relative min-h-[180px] sm:min-h-[200px] flex items-center justify-center">
                            <transition name="fade" mode="out-in">
                                <div :key="currentTestimonialIndex"
                                    class="w-full p-4 text-center border border-gray-200 rounded-lg sm:p-6 md:p-8 bg-gray-50 dark:bg-zinc-700 dark:border-zinc-600">
                                    <svg class="w-8 h-8 mx-auto mb-3 text-gray-300 sm:w-10 sm:h-10 sm:mb-4 dark:text-zinc-500"
                                        fill="currentColor" viewBox="0 0 24 24">
                                        <path
                                            d="M14.017 21v-7.391c0-5.704 3.731-9.57 8.983-10.609l.995 2.151c-2.432.917-3.995 3.638-3.995 5.849h4v10h-9.983zm-14.017 0v-7.391c0-5.704 3.748-9.57 9-10.609l.996 2.151c-2.433.917-3.996 3.638-3.996 5.849h3.983v10h-9.983z" />
                                    </svg>
                                    <p
                                        class="mb-4 text-base italic leading-relaxed text-gray-600 sm:text-lg sm:mb-6 md:text-xl dark:text-gray-300">
                                        "{{ testimonials[currentTestimonialIndex].quote }}"
                                    </p>
                                    <p class="text-base font-semibold text-gray-900 sm:text-lg dark:text-white">
                                        --{{ testimonials[currentTestimonialIndex].name }}
                                    </p>
                                </div>
                            </transition>
                        </div>
                    </div>

                    <!-- Navigation Arrows - visible on all screens now -->
                    <button @click="prevTestimonial"
                        class="absolute left-0 p-1 text-gray-500 -translate-y-1/2 rounded-full sm:p-2 sm:-ml-4 md:-ml-12 top-1/2 hover:text-gray-700 hover:bg-gray-100 dark:text-gray-400 dark:hover:text-white dark:hover:bg-zinc-600">
                        <svg class="w-5 h-5 sm:w-6 sm:h-6" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M15 19l-7-7 7-7" />
                        </svg>
                    </button>
                    <button @click="nextTestimonial"
                        class="absolute right-0 p-1 text-gray-500 -translate-y-1/2 rounded-full sm:p-2 sm:-mr-4 md:-mr-12 top-1/2 hover:text-gray-700 hover:bg-gray-100 dark:text-gray-400 dark:hover:text-white dark:hover:bg-zinc-600">
                        <svg class="w-5 h-5 sm:w-6 sm:h-6" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M9 5l7 7-7 7" />
                        </svg>
                    </button>

                    <!-- Dot Indicators -->
                    <div class="flex justify-center gap-2 mt-4 sm:mt-6">
                        <button v-for="(testimonial, index) in testimonials" :key="index"
                            @click="goToTestimonial(index)" class="w-2 h-2 transition-all rounded-full sm:w-3 sm:h-3"
                            :class="currentTestimonialIndex === index ? 'bg-[#112e51] dark:bg-[#02bfe7] scale-110' : 'bg-gray-300 dark:bg-zinc-600 hover:bg-gray-400'">
                        </button>
                    </div>
                </div>
            </div>
        </section>

        <!-- Recently Added Applications Section -->
        <section
            class="py-10 border-t border-gray-200 sm:py-12 bg-gray-50 dark:bg-zinc-900 md:py-16 dark:border-zinc-700">
            <div class="grid-container">
                <h2
                    class="mb-6 text-xl font-bold leading-tight text-center text-gray-900 sm:mb-8 sm:text-2xl md:text-3xl dark:text-white">
                    We've recently added the following number of applications:
                </h2>
                <div class="grid max-w-3xl grid-cols-1 gap-3 mx-auto sm:gap-4 sm:grid-cols-3">
                    <div
                        class="p-4 text-center bg-white border border-gray-200 rounded-lg sm:p-6 dark:bg-zinc-800 dark:border-zinc-700">
                        <div class="text-xs font-medium text-gray-500 sm:text-sm dark:text-gray-400">Last 7 Days:</div>
                        <div class="text-xl font-bold sm:text-2xl text-[#112e51] dark:text-[#02bfe7]">1,310</div>
                        <div class="text-xs text-gray-600 sm:text-sm dark:text-gray-400">new applications</div>
                    </div>
                    <div
                        class="p-4 text-center bg-white border border-gray-200 rounded-lg sm:p-6 dark:bg-zinc-800 dark:border-zinc-700">
                        <div class="text-xs font-medium text-gray-500 sm:text-sm dark:text-gray-400">Last 30 Days:</div>
                        <div class="text-xl font-bold sm:text-2xl text-[#112e51] dark:text-[#02bfe7]">6,068</div>
                        <div class="text-xs text-gray-600 sm:text-sm dark:text-gray-400">new applications</div>
                    </div>
                    <div
                        class="p-4 text-center bg-white border border-gray-200 rounded-lg sm:p-6 dark:bg-zinc-800 dark:border-zinc-700">
                        <div class="text-xs font-medium text-gray-500 sm:text-sm dark:text-gray-400">Last 90 Days:</div>
                        <div class="text-xl font-bold sm:text-2xl text-[#112e51] dark:text-[#02bfe7]">15,785</div>
                        <div class="text-xs text-gray-600 sm:text-sm dark:text-gray-400">new applications</div>
                    </div>
                </div>
            </div>
        </section>

        <!-- Where Does The Money Come From Section -->
        <section
            class="py-10 border-t border-gray-200 sm:py-12 bg-gray-50 dark:bg-zinc-900 md:py-16 dark:border-zinc-700">
            <div class="grid-container">
                <h2
                    class="mb-4 text-xl font-bold text-center text-gray-900 sm:mb-6 sm:text-2xl md:text-3xl dark:text-white">
                    Where does the money come from?
                </h2>
                <div class="max-w-3xl mx-auto">
                    <p class="mb-4 text-sm leading-relaxed text-gray-600 sm:mb-6 sm:text-base dark:text-gray-400">
                        The government always achieves the greatest success for its citizens and taxpayers by ensuring
                        that funds made from the 50 million barrels of high-quality sanctioned oil from Venezuela are
                        paid to the citizens as a resource benefit. In pursuing reforms, the government must balance its
                        responsibilities with the goal of providing effective payments and transparent disbursement of
                        funds to millions of beneficiaries in the United States.
                    </p>
                    <p class="mb-4 text-sm leading-relaxed text-gray-600 sm:mb-6 sm:text-base dark:text-gray-400">
                        These benefits assist the public and also help them understand what our government is doing to
                        overcome challenges and obstacles facing the nation and our citizens. This program is to ensure
                        that federal sanctioned resource funds reach the right recipients.
                    </p>
                    <div class="mt-6 text-center sm:mt-8">
                        <a :href="route('register')"
                            class="inline-block px-5 py-2.5 sm:px-6 sm:py-3 text-sm sm:text-base bg-[#112e51] text-white font-semibold rounded hover:bg-[#205493] transition-colors">
                            Start the Application Process
                        </a>
                    </div>
                </div>
            </div>
        </section>

        <!-- Corporate Quotes / News Section -->
        <section
            class="relative min-h-[400px] sm:min-h-[450px] md:min-h-[500px] lg:min-h-[625px] bg-scroll sm:bg-fixed bg-center bg-no-repeat bg-cover flex items-center"
            style="background-image: url('/images/city-optimized.jpg');">

            <div class="relative z-10 w-full px-4 py-10 sm:py-16 md:py-24">
                <!-- Main Quote -->
                <div class="mb-8 text-center sm:mb-12">
                    <h4
                        class="px-2 mb-4 text-lg font-semibold leading-tight text-gray-900 sm:text-xl sm:mb-6 md:text-2xl lg:text-3xl">
                        "Federal Resource Program distributes $2.4 billion to eligible Americans"
                    </h4>
                    <img src="https://upload.wikimedia.org/wikipedia/commons/c/ce/X_logo_2023.svg" alt="News Source"
                        class="h-6 mx-auto sm:h-8 opacity-70" />
                </div>

                <!-- Sub Quotes Grid -->
                <div
                    class="grid max-w-4xl grid-cols-1 gap-6 px-2 mx-auto sm:gap-8 sm:grid-cols-2 md:grid-cols-3 md:gap-10">
                    <div class="text-center">
                        <h4
                            class="mb-3 text-base font-medium leading-tight text-gray-800 sm:mb-4 sm:text-lg md:text-xl">
                            "Citizens receive funds from sanctioned oil reserves"
                        </h4>
                        <img src="https://upload.wikimedia.org/wikipedia/commons/thumb/2/2f/Google_2015_logo.svg/200px-Google_2015_logo.svg.png"
                            alt="Google News" class="h-5 mx-auto sm:h-6 opacity-80" />
                    </div>

                    <div class="text-center">
                        <h4
                            class="mb-3 text-base font-medium leading-tight text-gray-800 sm:mb-4 sm:text-lg md:text-xl">
                            "Resource Benefits: A Win for American Families"
                        </h4>
                        <img src="https://upload.wikimedia.org/wikipedia/commons/thumb/a/a9/Amazon_logo.svg/200px-Amazon_logo.svg.png"
                            alt="Amazon News" class="h-5 mx-auto sm:h-6 opacity-80" />
                    </div>

                    <div class="text-center sm:col-span-2 md:col-span-1">
                        <h4
                            class="mb-3 text-base font-medium leading-tight text-gray-800 sm:mb-4 sm:text-lg md:text-xl">
                            "New Community Programs Award...more than $50,000"
                        </h4>
                        <img src="https://upload.wikimedia.org/wikipedia/commons/thumb/5/51/IBM_logo.svg/200px-IBM_logo.svg.png"
                            alt="IBM News" class="h-5 mx-auto sm:h-6 opacity-80" />
                    </div>
                </div>
            </div>
        </section>

        <!-- Get Your Piece Section -->
        <section
            class="py-10 bg-white border-t border-gray-200 sm:py-12 dark:bg-zinc-800 md:py-16 dark:border-zinc-700">
            <div class="grid-container">
                <div class="max-w-3xl mx-auto text-center">
                    <h2 class="mb-4 text-xl font-bold text-gray-900 sm:mb-6 sm:text-2xl md:text-3xl dark:text-white">
                        Get your piece of the funding pie.
                    </h2>
                    <p class="mb-4 text-sm leading-relaxed text-gray-600 sm:mb-6 sm:text-base dark:text-gray-400">
                        Don't worry about your present financial situation. Funding applications do not require
                        collateral, credit checks, security deposits or co-signers. You can apply even if you have
                        a Bankruptcy. Also remember that government funding is not available for personal expenses
                        or paying off debt but there may be other assistance programs out there to help you out of
                        your situation!
                    </p>
                    <a :href="route('register')"
                        class="inline-block px-5 py-2.5 mb-4 sm:px-8 sm:py-3 sm:mb-6 text-sm sm:text-base bg-[#112e51] text-white font-semibold rounded hover:bg-[#205493] transition-colors">
                        Start the Application Process Now
                    </a>
                    <p class="text-sm font-medium text-gray-700 sm:text-base dark:text-gray-300">
                        Don't wait. The money you need may be given to your neighbor, if they qualify!
                    </p>
                </div>
            </div>
        </section>

        <!-- Grant Application Services Section -->
        <section
            class="py-10 border-t border-gray-200 sm:py-12 bg-gray-50 dark:bg-zinc-900 md:py-16 dark:border-zinc-700">
            <div class="grid-container">
                <div class="max-w-3xl mx-auto">
                    <h2
                        class="mb-4 text-xl font-bold text-center text-gray-900 sm:mb-6 sm:text-2xl md:text-3xl dark:text-white">
                        Grant Application Services
                    </h2>
                    <p class="mb-4 text-sm leading-relaxed text-gray-600 sm:mb-6 sm:text-base dark:text-gray-400">
                        In order for us to pay for the expenses we have incurred compiling this information, we are
                        forced to charge a small fee to cover our costs. The information also comes with a guarantee.
                    </p>
                    <p class="mb-4 text-sm leading-relaxed text-gray-600 sm:mb-6 sm:text-base dark:text-gray-400">
                        If you apply for funding using one of the applications you find on our website and you don't
                        receive funding, your membership fees will be refunded to you immediately. All you have to do
                        is provide us with the name of the funding opportunity you applied for and a copy of the
                        rejection letter you received from the funder.
                    </p>
                    <p class="mb-4 text-sm leading-relaxed text-gray-600 sm:mb-6 sm:text-base dark:text-gray-400">
                        With your membership fee, you will have access to the applications and online training
                        information through our members-only site. You can also cancel your membership at any time.
                    </p>
                    <p class="mb-4 text-sm leading-relaxed text-gray-600 sm:mb-6 sm:text-base dark:text-gray-400">
                        How serious are you? Are you like most people and needed the money yesterday? If so, get
                        registered today. Don't procrastinate. We are here to help you get the info you need today,
                        not next year. <strong>"Change your future in 2026."</strong>
                    </p>
                    <p class="mb-6 text-sm leading-relaxed text-gray-600 sm:mb-8 sm:text-base dark:text-gray-400">
                        If FREE FUNDING to start a new business, expand your business or complete a worthwhile project
                        would change your life and benefit you for the rest of your life, what are you waiting for?
                        Get registered TODAY. We can help you find and apply for the money you need to change your
                        life forever. We are Funding Seeking Specialists.
                    </p>
                    <div class="text-center">
                        <a :href="route('register')"
                            class="inline-block px-5 py-2.5 sm:px-8 sm:py-3 text-sm sm:text-base bg-[#112e51] text-white font-semibold rounded hover:bg-[#205493] transition-colors">
                            Begin the process now!
                        </a>
                    </div>
                </div>
            </div>
        </section>

        <!-- Footer -->
        <footer class="bg-[#112e51] text-white mt-10 sm:mt-16">
            <!-- Main Footer -->
            <div class="px-4 py-8 mx-auto sm:py-12 max-w-7xl sm:px-6 lg:px-8">
                <div class="grid grid-cols-1 gap-8 sm:grid-cols-2 md:grid-cols-4">
                    <!-- Logo & Description -->
                    <div class="sm:col-span-2">
                        <div class="flex items-center gap-2 mb-3 sm:gap-3 sm:mb-4">
                            <!-- Use admin-configured logo if available -->
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
                            payments
                            and reducing fraud, waste, and abuse.
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
                            <a v-if="settings.social.tiktok" :href="settings.social.tiktok" target="_blank"
                                rel="noopener noreferrer" class="text-gray-400 transition hover:text-white"
                                title="TikTok">
                                <svg class="w-5 h-5" fill="currentColor" viewBox="0 0 24 24">
                                    <path
                                        d="M12.525.02c1.31-.02 2.61-.01 3.91-.02.08 1.53.63 3.09 1.75 4.17 1.12 1.11 2.7 1.62 4.24 1.79v4.03c-1.44-.05-2.89-.35-4.2-.97-.57-.26-1.1-.59-1.62-.93-.01 2.92.01 5.84-.02 8.75-.08 1.4-.54 2.79-1.35 3.94-1.31 1.92-3.58 3.17-5.91 3.21-1.43.08-2.86-.31-4.08-1.03-2.02-1.19-3.44-3.37-3.65-5.71-.02-.5-.03-1-.01-1.49.18-1.9 1.12-3.72 2.58-4.96 1.66-1.44 3.98-2.13 6.15-1.72.02 1.48-.04 2.96-.04 4.44-.99-.32-2.15-.23-3.02.37-.63.41-1.11 1.04-1.36 1.75-.21.51-.15 1.07-.14 1.61.24 1.64 1.82 3.02 3.5 2.87 1.12-.01 2.19-.66 2.77-1.61.19-.33.4-.67.41-1.06.1-1.79.06-3.57.07-5.36.01-4.03-.01-8.05.02-12.07z" />
                                </svg>
                            </a>
                        </div>
                    </div>

                    <!-- Quick Links -->
                    <div>
                        <h4 class="font-bold text-[#02bfe7] mb-3 sm:mb-4 text-sm sm:text-base">Quick Links</h4>
                        <ul class="space-y-1 text-xs sm:space-y-2 sm:text-sm">
                            <li><a href="#" class="text-gray-300 transition hover:text-white">About Us</a></li>
                            <li><a href="#" class="text-gray-300 transition hover:text-white">Programs</a></li>
                            <li><a href="#" class="text-gray-300 transition hover:text-white">Resources</a></li>
                            <li><a href="#" class="text-gray-300 transition hover:text-white">Contact</a></li>
                            <li><a href="#" class="text-gray-300 transition hover:text-white">FAQ</a></li>
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

<style scoped>
/* Smooth scrolling */
html {
    scroll-behavior: smooth;
}

/* Responsive grid container */
.grid-container {
    margin-left: auto;
    margin-right: auto;
    max-width: 64rem;
    padding-left: 1rem;
    padding-right: 1rem;
    width: 100%;
    box-sizing: border-box;
}

@media (min-width: 640px) {
    .grid-container {
        padding-left: 1.5rem;
        padding-right: 1.5rem;
    }
}

@media (min-width: 1024px) {
    .grid-container {
        padding-left: 2rem;
        padding-right: 2rem;
    }
}

/* Testimonial carousel fade transition */
.fade-enter-active,
.fade-leave-active {
    transition: opacity 0.5s ease;
}

.fade-enter-from,
.fade-leave-to {
    opacity: 0;
}

/* Prevent text overflow on mobile */
.break-words {
    word-wrap: break-word;
    overflow-wrap: break-word;
    hyphens: auto;
}
</style>
