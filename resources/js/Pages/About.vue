<script setup>
import PublicLayout from '@/Layouts/PublicLayout.vue';
import { Link, usePage } from '@inertiajs/vue3';
import { computed } from 'vue';

const page = usePage();
const settings = computed(() => page.props.settings || {});
const siteName = computed(() => settings.value.site_name || 'NationalResourceBenefits.gov');
</script>

<template>
    <PublicLayout title="About">
        <!-- Hero Section -->
        <section class="relative py-12 md:py-20 bg-[#112e51]">
            <div class="px-4 mx-auto max-w-7xl sm:px-6 lg:px-8">
                <div class="max-w-4xl">
                    <h1 class="mb-4 text-3xl font-bold text-white md:text-4xl lg:text-5xl">
                        About {{ siteName }}
                    </h1>
                    <p class="text-lg text-gray-300 md:text-xl">
                        Ensuring transparent disbursement of sanctioned resource funds to eligible citizens.
                    </p>
                </div>
            </div>
        </section>

        <!-- Main Content Section -->
        <section class="py-12 bg-white md:py-16">
            <div class="px-4 mx-auto max-w-7xl sm:px-6 lg:px-8">
                <div class="max-w-4xl">
                    <div class="prose prose-lg max-w-none">
                        <p class="text-gray-700 leading-relaxed text-lg mb-6">
                            Effective stewardship of sanctioned oil and resource funds is a critical responsibility of
                            the Federal Government. Our goal is to ensure that proceeds derived from sanctioned oil
                            seized by the United States government from Venezuela and the Nicolás Maduro regime are
                            properly and transparently disbursed to eligible citizens.
                        </p>

                        <p class="text-gray-700 leading-relaxed text-lg mb-6">
                            These benefits are intended for lawful, tax-paying citizens of the United States, as well
                            as citizens of Venezuela who are legal residents of the United States. This site is
                            dedicated
                            to ensuring the American people that its government is disbursing every single penny made
                            from all the sanctioned resources.
                        </p>

                        <p class="text-gray-700 leading-relaxed text-lg mb-6">
                            We are addressing and taking concrete steps to prevent improper payments. The funds will
                            only be paid to eligible individuals.
                        </p>
                    </div>
                </div>
            </div>
        </section>

        <!-- What is an Improper Payment Section -->
        <section class="py-12 bg-gray-50 md:py-16">
            <div class="px-4 mx-auto max-w-7xl sm:px-6 lg:px-8">
                <div class="max-w-4xl">
                    <h2 class="mb-6 text-2xl font-bold text-gray-900 md:text-3xl">
                        What is an Improper Payment?
                    </h2>
                    <div class="prose prose-lg max-w-none">
                        <p class="text-gray-700 leading-relaxed mb-6">
                            An improper payment is any payment that should not have been made or that was made in an
                            incorrect amount under statutory, contractual, administrative, or other legally applicable
                            requirements.
                        </p>

                        <p class="text-gray-700 leading-relaxed mb-4">
                            The term "improper payment" consists of two main components:
                        </p>

                        <ul class="list-disc pl-6 mb-6 space-y-3 text-gray-700">
                            <li>
                                <strong>Monetary loss to the Government:</strong> Payments made to the wrong recipient
                                and/or in the wrong amount.
                            </li>
                            <li>
                                <strong>Non-monetary loss:</strong> Improper payments that do not result in a monetary
                                loss, including under-payments and payments made to the right recipient for the right
                                amount, but not made in strict accordance with statute or regulation.
                            </li>
                        </ul>

                        <p class="text-gray-700 leading-relaxed">
                            Although working to reduce all improper payments is important, <strong>prevention of
                                improper
                                payments resulting in a monetary loss is the federal government's highest
                                priority.</strong>
                        </p>
                    </div>
                </div>
            </div>
        </section>

        <!-- Our Commitment Section -->
        <section class="py-12 bg-white md:py-16">
            <div class="px-4 mx-auto max-w-7xl sm:px-6 lg:px-8">
                <div class="max-w-4xl">
                    <h2 class="mb-6 text-2xl font-bold text-gray-900 md:text-3xl">
                        Our Commitment
                    </h2>

                    <div class="grid gap-6 md:grid-cols-2">
                        <div class="p-6 bg-gray-50 rounded-lg border-l-4 border-[#112e51]">
                            <div class="flex items-center gap-3 mb-3">
                                <div
                                    class="flex items-center justify-center w-10 h-10 rounded-full bg-[#112e51] text-white">
                                    <svg class="w-5 h-5" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2"
                                            d="M9 12l2 2 4-4m5.618-4.016A11.955 11.955 0 0112 2.944a11.955 11.955 0 01-8.618 3.04A12.02 12.02 0 003 9c0 5.591 3.824 10.29 9 11.622 5.176-1.332 9-6.03 9-11.622 0-1.042-.133-2.052-.382-3.016z" />
                                    </svg>
                                </div>
                                <h3 class="text-lg font-bold text-gray-900">Transparency</h3>
                            </div>
                            <p class="text-gray-600">
                                Ensuring complete visibility into how sanctioned resource funds are distributed
                                to eligible citizens.
                            </p>
                        </div>

                        <div class="p-6 bg-gray-50 rounded-lg border-l-4 border-[#112e51]">
                            <div class="flex items-center gap-3 mb-3">
                                <div
                                    class="flex items-center justify-center w-10 h-10 rounded-full bg-[#112e51] text-white">
                                    <svg class="w-5 h-5" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2"
                                            d="M12 15v2m-6 4h12a2 2 0 002-2v-6a2 2 0 00-2-2H6a2 2 0 00-2 2v6a2 2 0 002 2zm10-10V7a4 4 0 00-8 0v4h8z" />
                                    </svg>
                                </div>
                                <h3 class="text-lg font-bold text-gray-900">Security</h3>
                            </div>
                            <p class="text-gray-600">
                                Protecting your personal information with bank-level encryption and federal
                                data protection standards.
                            </p>
                        </div>

                        <div class="p-6 bg-gray-50 rounded-lg border-l-4 border-[#112e51]">
                            <div class="flex items-center gap-3 mb-3">
                                <div
                                    class="flex items-center justify-center w-10 h-10 rounded-full bg-[#112e51] text-white">
                                    <svg class="w-5 h-5" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2"
                                            d="M9 5H7a2 2 0 00-2 2v12a2 2 0 002 2h10a2 2 0 002-2V7a2 2 0 00-2-2h-2M9 5a2 2 0 002 2h2a2 2 0 002-2M9 5a2 2 0 012-2h2a2 2 0 012 2m-6 9l2 2 4-4" />
                                    </svg>
                                </div>
                                <h3 class="text-lg font-bold text-gray-900">Accountability</h3>
                            </div>
                            <p class="text-gray-600">
                                Taking concrete steps to prevent fraud, waste, and abuse in the distribution
                                of federal resources.
                            </p>
                        </div>

                        <div class="p-6 bg-gray-50 rounded-lg border-l-4 border-[#112e51]">
                            <div class="flex items-center gap-3 mb-3">
                                <div
                                    class="flex items-center justify-center w-10 h-10 rounded-full bg-[#112e51] text-white">
                                    <svg class="w-5 h-5" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2"
                                            d="M17 20h5v-2a3 3 0 00-5.356-1.857M17 20H7m10 0v-2c0-.656-.126-1.283-.356-1.857M7 20H2v-2a3 3 0 015.356-1.857M7 20v-2c0-.656.126-1.283.356-1.857m0 0a5.002 5.002 0 019.288 0M15 7a3 3 0 11-6 0 3 3 0 016 0zm6 3a2 2 0 11-4 0 2 2 0 014 0zM7 10a2 2 0 11-4 0 2 2 0 014 0z" />
                                    </svg>
                                </div>
                                <h3 class="text-lg font-bold text-gray-900">Eligibility</h3>
                            </div>
                            <p class="text-gray-600">
                                Ensuring funds reach only eligible individuals through thorough verification
                                and compliance processes.
                            </p>
                        </div>
                    </div>
                </div>
            </div>
        </section>

        <!-- Additional Links Section -->
        <section class="py-12 bg-gray-50 md:py-16 border-t border-gray-200">
            <div class="px-4 mx-auto max-w-7xl sm:px-6 lg:px-8">
                <div class="max-w-4xl">
                    <h2 class="mb-6 text-xl font-bold text-gray-900">Additional Links</h2>
                    <div class="grid gap-4 sm:grid-cols-2 md:grid-cols-3">
                        <a href="https://www.whitehouse.gov/omb/" target="_blank" rel="noopener noreferrer"
                            class="text-[#205493] hover:text-[#112e51] hover:underline flex items-center gap-2">
                            <svg class="w-4 h-4" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2"
                                    d="M10 6H6a2 2 0 00-2 2v10a2 2 0 002 2h10a2 2 0 002-2v-4M14 4h6m0 0v6m0-6L10 14" />
                            </svg>
                            About OMB
                        </a>
                        <a href="https://www.gsa.gov/website-information/accessibility-aids" target="_blank"
                            rel="noopener noreferrer"
                            class="text-[#205493] hover:text-[#112e51] hover:underline flex items-center gap-2">
                            <svg class="w-4 h-4" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2"
                                    d="M10 6H6a2 2 0 00-2 2v10a2 2 0 002 2h10a2 2 0 002-2v-4M14 4h6m0 0v6m0-6L10 14" />
                            </svg>
                            Accessibility Support
                        </a>
                        <a href="https://www.gsa.gov/reference/freedom-of-information-act-foia" target="_blank"
                            rel="noopener noreferrer"
                            class="text-[#205493] hover:text-[#112e51] hover:underline flex items-center gap-2">
                            <svg class="w-4 h-4" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2"
                                    d="M10 6H6a2 2 0 00-2 2v10a2 2 0 002 2h10a2 2 0 002-2v-4M14 4h6m0 0v6m0-6L10 14" />
                            </svg>
                            FOIA Requests
                        </a>
                        <a href="https://www.gsaig.gov/" target="_blank" rel="noopener noreferrer"
                            class="text-[#205493] hover:text-[#112e51] hover:underline flex items-center gap-2">
                            <svg class="w-4 h-4" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2"
                                    d="M10 6H6a2 2 0 00-2 2v10a2 2 0 002 2h10a2 2 0 002-2v-4M14 4h6m0 0v6m0-6L10 14" />
                            </svg>
                            Office of Inspector General
                        </a>
                        <a href="https://www.gsa.gov/website-information/website-policies" target="_blank"
                            rel="noopener noreferrer"
                            class="text-[#205493] hover:text-[#112e51] hover:underline flex items-center gap-2">
                            <svg class="w-4 h-4" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2"
                                    d="M10 6H6a2 2 0 00-2 2v10a2 2 0 002 2h10a2 2 0 002-2v-4M14 4h6m0 0v6m0-6L10 14" />
                            </svg>
                            Privacy Policy
                        </a>
                        <a href="https://www.gao.gov/about/what-gao-does/fraudnet/" target="_blank"
                            rel="noopener noreferrer"
                            class="text-[#205493] hover:text-[#112e51] hover:underline flex items-center gap-2">
                            <svg class="w-4 h-4" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2"
                                    d="M10 6H6a2 2 0 00-2 2v10a2 2 0 002 2h10a2 2 0 002-2v-4M14 4h6m0 0v6m0-6L10 14" />
                            </svg>
                            Report Fraud, Waste & Abuse
                        </a>
                    </div>
                </div>
            </div>
        </section>

        <!-- CTA Section -->
        <section class="py-12 bg-[#112e51] md:py-16">
            <div class="px-4 mx-auto text-center max-w-7xl sm:px-6 lg:px-8">
                <h2 class="mb-4 text-2xl font-bold text-white md:text-3xl">Check Your Eligibility</h2>
                <p class="max-w-2xl mx-auto mb-8 text-gray-300">
                    Find out if you qualify for benefits from sanctioned resource funds.
                </p>
                <div class="flex flex-col items-center justify-center gap-4 sm:flex-row">
                    <Link :href="route('register')"
                        class="px-8 py-3 text-[#112e51] font-semibold bg-[#02bfe7] rounded-lg hover:bg-[#00a6ce] transition-colors">
                    Get Started
                    </Link>
                    <a href="/faq"
                        class="px-8 py-3 font-semibold text-white border-2 border-white rounded-lg hover:bg-white hover:text-[#112e51] transition-colors">
                        View FAQ
                    </a>
                </div>
            </div>
        </section>

        <!-- Government Links Footer -->
        <section class="py-6 bg-gray-100 border-t border-gray-200">
            <div class="px-4 mx-auto max-w-7xl sm:px-6 lg:px-8">
                <div class="flex flex-wrap items-center justify-center gap-4 text-sm">
                    <a href="https://www.performance.gov/" target="_blank" rel="noopener noreferrer"
                        class="text-[#205493] hover:underline">Performance.gov</a>
                    <span class="text-gray-400">|</span>
                    <a href="https://www.whitehouse.gov/" target="_blank" rel="noopener noreferrer"
                        class="text-[#205493] hover:underline">Whitehouse.gov</a>
                    <span class="text-gray-400">|</span>
                    <a href="https://www.usa.gov/" target="_blank" rel="noopener noreferrer"
                        class="text-[#205493] hover:underline">USA.gov</a>
                    <span class="text-gray-400">|</span>
                    <a href="https://www.usaspending.gov/" target="_blank" rel="noopener noreferrer"
                        class="text-[#205493] hover:underline">USAspending.gov</a>
                    <span class="text-gray-400">|</span>
                    <a href="https://fiscal.treasury.gov/" target="_blank" rel="noopener noreferrer"
                        class="text-[#205493] hover:underline">Fiscal.Treasury.gov</a>
                </div>
            </div>
        </section>
    </PublicLayout>
</template>
