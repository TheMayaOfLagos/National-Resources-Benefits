<script setup>
import PublicLayout from '@/Layouts/PublicLayout.vue';
import { Link, usePage } from '@inertiajs/vue3';
import { computed } from 'vue';

const page = usePage();
const settings = computed(() => page.props.settings || {});
const siteName = computed(() => settings.value.site_name || 'NationalResourceBenefits.gov');

const scrollToTop = () => {
    window.scrollTo({ top: 0, behavior: 'smooth' });
};
</script>

<template>
    <PublicLayout title="Frequently Asked Questions">
        <!-- Hero Section -->
        <section class="relative py-12 md:py-20 bg-[#112e51]">
            <div class="px-4 mx-auto max-w-7xl sm:px-6 lg:px-8">
                <div class="max-w-4xl">
                    <h1 class="mb-4 text-3xl font-bold text-white md:text-4xl lg:text-5xl">
                        Frequently Asked Questions about the website
                    </h1>
                </div>
            </div>
        </section>

        <!-- FAQ Content -->
        <section class="py-12 bg-white md:py-16">
            <div class="px-4 mx-auto max-w-7xl sm:px-6 lg:px-8">
                <div class="max-w-4xl">
                    <!-- Question 1 -->
                    <div class="mb-10">
                        <h4 class="mb-4 text-xl font-bold text-gray-900">
                            What is the purpose of this website?
                        </h4>
                        <p class="leading-relaxed text-gray-700">
                            As required by <a href="/documents/Executive_Order_13520.pdf" target="_blank"
                                class="text-[#205493] underline hover:text-[#112e51]">Executive Order 13520 (codified in
                                31 USC 3321)</a> dated January 7, 2026,
                            disbursing federal benefit payments, the U.S. Department of the Treasury, in coordination
                            with the U.S. Department of Justice and the Office of Management and Budget, established
                            this website to create a centralized system to disburse the funds made from the sale of
                            sanctioned Venezuelan oil and publish information about payments made to individuals,
                            organizations, and contractors. This website also provides a centralized place where the
                            public can report suspected incidents of fraud, waste, and abuse. The customer service
                            link for reporting fraud, waste, and abuse is located at the bottom of every
                            {{ siteName }} page.
                        </p>
                    </div>

                    <!-- Question 2 -->
                    <div class="mb-10">
                        <h4 class="mb-4 text-xl font-bold text-gray-900">
                            What information is included in this website?
                        </h4>
                        <p class="leading-relaxed text-gray-700">
                            This website includes current information about how to receive your benefits and the
                            disbursement of payments made under sanctioned resources. Please consult the government
                            agency customer service through the website for help or advice on how to create your
                            account and receive your benefits.
                        </p>
                    </div>

                    <!-- Question 3 -->
                    <div class="mb-10">
                        <h4 class="mb-4 text-xl font-bold text-gray-900">
                            Can I download data?
                        </h4>
                        <p class="leading-relaxed text-gray-700">
                            Yes, you can download your account information and transaction history after receiving
                            your benefits.
                        </p>
                    </div>

                    <!-- Question 4 -->
                    <div class="mb-10">
                        <h4 class="mb-4 text-xl font-bold text-gray-900">
                            I can't find a specific program or agency? Is their data available?
                        </h4>
                        <p class="leading-relaxed text-gray-700">
                            Each department, agency, or instrumentality in the executive branch of the United States
                            Government has its own website and responsibilities. If you are looking for other agency
                            services, we recommend that you visit their official websites. This website is designed
                            specifically for the payout of sanctioned resource benefits. In collaboration with the
                            Department of the Treasury, we only offer disbursement services and provide a centralized
                            system where the public can report suspected incidents of fraud, waste, and abuse.
                        </p>
                    </div>

                    <!-- Question 5 -->
                    <div class="mb-10">
                        <h4 class="mb-4 text-xl font-bold text-gray-900">
                            How is {{ siteName }} different from USASpending.gov?
                        </h4>
                        <p class="leading-relaxed text-gray-700">
                            While USASpending.gov shows the public where their money is being spent—by programs or
                            activities—{{ siteName }} serves as a system for the disbursement of benefit funds
                            efficiently and effectively. Federal payments are being made by our agency through this
                            website. In other words, is the government paying the right person, at the right time,
                            for the right amount? This is why we were created to make sure the funds are disbursed
                            legally and efficiently. We help make sure the funds get to the right eligible citizens
                            of the United States and citizens of Venezuela who are legal residents of the United States.
                        </p>
                    </div>

                    <!-- Question 6 -->
                    <div class="mb-10">
                        <h4 class="mb-4 text-xl font-bold text-gray-900">
                            What steps should the American public take to receive their benefits?
                        </h4>
                        <ol class="pl-6 space-y-3 text-gray-700 list-decimal">
                            <li>Create an account on this website by providing the required registration information.
                            </li>
                            <li>Verify your identity by uploading a valid government-issued identification document.
                            </li>
                            <li>Submit your application and wait for verification and approval.</li>
                            <li>Once approved, your benefits will be deposited into your account on this website.</li>
                            <li>After the deposit is completed, you may transfer the funds to your personal bank
                                account.</li>
                        </ol>
                    </div>

                    <!-- Question 7 -->
                    <div class="mb-10">
                        <h4 class="mb-4 text-xl font-bold text-gray-900">
                            How does someone report suspected incidents of fraud, waste and abuse related to improper
                            payments?
                        </h4>
                        <p class="leading-relaxed text-gray-700">
                            This website also provides information on where the public can report suspected incidents
                            of fraud, waste, and abuse by an entity receiving federal benefit funds that have led,
                            or may lead, to improper payments by the Federal Government. The customer service link
                            on the website can also be used to report fraud, waste, and abuse. It is located at the
                            bottom right of every page. Please contact us at
                            <a href="tel:+13013644016" class="text-[#205493] hover:underline">(301) 364-4016</a>
                            to notify us of any issues or to request assistance with the website, including data,
                            website and document links, or other information.
                        </p>
                    </div>

                    <!-- Return to top link -->
                    <div class="pt-8 mt-8 border-t border-gray-200">
                        <button @click="scrollToTop"
                            class="text-[#205493] hover:underline inline-flex items-center gap-1">
                            <svg class="w-4 h-4" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2"
                                    d="M5 10l7-7m0 0l7 7m-7-7v18" />
                            </svg>
                            Return to top
                        </button>
                    </div>
                </div>
            </div>
        </section>

        <!-- Additional Links Section -->
        <section class="py-12 border-t border-gray-200 bg-gray-50 md:py-16">
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
                        <a href="https://www.gsa.gov/reference/civil-rights-programs/notification-and-federal-employee-antidiscrimination-and-retaliation-act-of-2002"
                            target="_blank" rel="noopener noreferrer"
                            class="text-[#205493] hover:text-[#112e51] hover:underline flex items-center gap-2">
                            <svg class="w-4 h-4" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2"
                                    d="M10 6H6a2 2 0 00-2 2v10a2 2 0 002 2h10a2 2 0 002-2v-4M14 4h6m0 0v6m0-6L10 14" />
                            </svg>
                            No FEAR Act Data
                        </a>
                        <a href="https://www.gsaig.gov/" target="_blank" rel="noopener noreferrer"
                            class="text-[#205493] hover:text-[#112e51] hover:underline flex items-center gap-2">
                            <svg class="w-4 h-4" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2"
                                    d="M10 6H6a2 2 0 00-2 2v10a2 2 0 002 2h10a2 2 0 002-2v-4M14 4h6m0 0v6m0-6L10 14" />
                            </svg>
                            Office of Inspector General
                        </a>
                        <a href="https://www.gsa.gov/reference/reports/budget-performance" target="_blank"
                            rel="noopener noreferrer"
                            class="text-[#205493] hover:text-[#112e51] hover:underline flex items-center gap-2">
                            <svg class="w-4 h-4" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2"
                                    d="M10 6H6a2 2 0 00-2 2v10a2 2 0 002 2h10a2 2 0 002-2v-4M14 4h6m0 0v6m0-6L10 14" />
                            </svg>
                            Performance Reports
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
                    </div>
                </div>
            </div>
        </section>

        <!-- Government Links Footer -->
        <section class="py-6 bg-gray-100 border-t border-gray-200">
            <div class="px-4 mx-auto max-w-7xl sm:px-6 lg:px-8">
                <div class="flex flex-wrap items-center justify-center gap-4 mb-4 text-sm">
                    <a href="https://www.usaspending.gov/#/about/accessibility" target="_blank"
                        rel="noopener noreferrer" class="text-[#205493] hover:underline">Accessibility</a>
                    <span class="text-gray-400">|</span>
                    <a href="https://www.usaspending.gov/#/about/privacy" target="_blank" rel="noopener noreferrer"
                        class="text-[#205493] hover:underline">Privacy Policy</a>
                </div>
                <div class="flex flex-wrap items-center justify-center gap-4 mb-4 text-sm">
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
                <div class="flex justify-center">
                    <a href="https://www.gao.gov/about/what-gao-does/fraudnet/" target="_blank"
                        rel="noopener noreferrer" class="text-[#205493] hover:underline font-medium">Report Fraud, Waste
                        and Abuse</a>
                </div>
            </div>
        </section>

        <!-- Site Identifier -->
        <section class="py-4 bg-[#112e51]">
            <div class="px-4 mx-auto text-center max-w-7xl sm:px-6 lg:px-8">
                <p class="font-medium text-white">{{ siteName }}</p>
                <p class="mt-1 text-sm text-gray-400">An official website of the Federal Government</p>
                <p class="mt-2 text-sm text-gray-400">
                    Looking for U.S. government information and services?
                    <a href="https://www.usa.gov/" target="_blank" rel="noopener noreferrer"
                        class="text-[#02bfe7] hover:underline">Visit USA.gov</a>
                </p>
            </div>
        </section>
    </PublicLayout>
</template>
