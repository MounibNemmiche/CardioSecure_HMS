<script setup lang="ts">
import { usePage } from '@inertiajs/vue3'

const page = usePage()
const auth = page.props.auth as { user: { name: string; roles: string[] } | null }
</script>

<template>
    <div class="min-h-screen bg-white flex flex-col">
        <!-- Header -->
        <header class="sticky top-0 z-30 border-b border-gray-100 bg-white/90 backdrop-blur-sm">
            <div class="max-w-6xl mx-auto px-6 py-4 flex items-center justify-between">
                <div class="flex items-center gap-3">
                    <div class="w-10 h-10 bg-blue-800 rounded-xl flex items-center justify-center shadow-sm">
                        <svg class="w-6 h-6 text-white" fill="none" viewBox="0 0 24 24" stroke="currentColor">
                            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2"
                                d="M4.318 6.318a4.5 4.5 0 000 6.364L12 20.364l7.682-7.682a4.5 4.5 0 00-6.364-6.364L12 7.636l-1.318-1.318a4.5 4.5 0 00-6.364 0z" />
                        </svg>
                    </div>
                    <span class="text-lg font-bold text-gray-900">CardioSecure</span>
                </div>
                <div class="flex items-center gap-3">
                    <template v-if="auth.user">
                        <a
                            :href="auth.user.roles[0] === 'patient' ? '/patient/dashboard'
                                : auth.user.roles[0] === 'doctor' ? '/doctor/dashboard'
                                : auth.user.roles[0] === 'staff' ? '/staff/dashboard'
                                : '/admin/dashboard'"
                            class="inline-flex items-center px-4 py-2 rounded-lg text-sm font-semibold text-white bg-blue-800 hover:bg-blue-900 transition-colors duration-200 cursor-pointer"
                        >
                            Go to Dashboard
                        </a>
                    </template>
                    <template v-else>
                        <a href="/login" class="text-sm font-medium text-gray-600 hover:text-gray-900 transition-colors duration-200 cursor-pointer">Login</a>
                        <a
                            href="/register"
                            class="inline-flex items-center px-4 py-2 rounded-lg text-sm font-semibold text-white bg-blue-800 hover:bg-blue-900 transition-colors duration-200 cursor-pointer shadow-sm"
                        >
                            Register
                        </a>
                    </template>
                </div>
            </div>
        </header>

        <!-- Hero -->
        <section class="flex-1 flex flex-col items-center justify-center text-center px-6 py-20 sm:py-28 bg-linear-to-b from-blue-50/60 to-white">
            <div class="w-20 h-20 bg-blue-800 rounded-2xl flex items-center justify-center mb-6 shadow-lg shadow-blue-800/20">
                <svg class="w-12 h-12 text-white" fill="none" viewBox="0 0 24 24" stroke="currentColor">
                    <path stroke-linecap="round" stroke-linejoin="round" stroke-width="1.5"
                        d="M4.318 6.318a4.5 4.5 0 000 6.364L12 20.364l7.682-7.682a4.5 4.5 0 00-6.364-6.364L12 7.636l-1.318-1.318a4.5 4.5 0 00-6.364 0z" />
                </svg>
            </div>

            <h1 class="text-4xl sm:text-5xl font-extrabold text-gray-900 mb-4 tracking-tight">CardioSecure</h1>
            <p class="text-lg text-gray-500 max-w-lg mb-10 leading-relaxed">
                Secure cardiology clinic information system. Manage appointments, medical records, and medication reminders - all in one place.
            </p>

            <div class="flex flex-col sm:flex-row gap-4">
                <a
                    href="/login"
                    class="inline-flex items-center justify-center px-8 py-3 rounded-xl text-base font-semibold text-white bg-blue-800 hover:bg-blue-900 shadow-md shadow-blue-800/15 transition-all duration-200 cursor-pointer hover:shadow-lg"
                >
                    Login
                </a>
                <a
                    href="/register"
                    class="inline-flex items-center justify-center px-8 py-3 border-2 border-blue-800 rounded-xl text-base font-semibold text-blue-800 hover:bg-blue-50 transition-colors duration-200 cursor-pointer"
                >
                    Register as Patient
                </a>
            </div>
        </section>

        <!-- Features -->
        <section class="py-16 sm:py-20 px-6 bg-white">
            <div class="max-w-6xl mx-auto">
                <h2 class="text-2xl sm:text-3xl font-bold text-gray-900 text-center mb-4">Everything your clinic needs</h2>
                <p class="text-gray-500 text-center max-w-lg mx-auto mb-12">A complete digital healthcare platform designed for cardiologists, staff, and patients.</p>

                <div class="grid grid-cols-1 sm:grid-cols-2 lg:grid-cols-3 gap-8">
                    <!-- Appointments -->
                    <div class="bg-gray-50 rounded-2xl p-6 border border-gray-100 hover:border-blue-200 transition-colors duration-200">
                        <div class="w-10 h-10 bg-blue-100 rounded-lg flex items-center justify-center mb-4">
                            <svg class="w-5 h-5 text-blue-800" fill="none" viewBox="0 0 24 24" stroke="currentColor">
                                <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M8 7V3m8 4V3m-9 8h10M5 21h14a2 2 0 002-2V7a2 2 0 00-2-2H5a2 2 0 00-2 2v12a2 2 0 002 2z" />
                            </svg>
                        </div>
                        <h3 class="font-semibold text-gray-900 mb-2">Appointment Booking</h3>
                        <p class="text-sm text-gray-500 leading-relaxed">Patients book visits online. Staff manage the full schedule with rescheduling and calendar views.</p>
                    </div>

                    <!-- Medical Records -->
                    <div class="bg-gray-50 rounded-2xl p-6 border border-gray-100 hover:border-blue-200 transition-colors duration-200">
                        <div class="w-10 h-10 bg-blue-100 rounded-lg flex items-center justify-center mb-4">
                            <svg class="w-5 h-5 text-blue-800" fill="none" viewBox="0 0 24 24" stroke="currentColor">
                                <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M9 12h6m-6 4h6m2 5H7a2 2 0 01-2-2V5a2 2 0 012-2h5.586a1 1 0 01.707.293l5.414 5.414a1 1 0 01.293.707V19a2 2 0 01-2 2z" />
                            </svg>
                        </div>
                        <h3 class="font-semibold text-gray-900 mb-2">Medical Records</h3>
                        <p class="text-sm text-gray-500 leading-relaxed">Immutable cardiology records with vitals, symptoms, diagnoses, and file attachments like ECG results.</p>
                    </div>

                    <!-- Medication Reminders -->
                    <div class="bg-gray-50 rounded-2xl p-6 border border-gray-100 hover:border-blue-200 transition-colors duration-200">
                        <div class="w-10 h-10 bg-blue-100 rounded-lg flex items-center justify-center mb-4">
                            <svg class="w-5 h-5 text-blue-800" fill="none" viewBox="0 0 24 24" stroke="currentColor">
                                <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M15 17h5l-1.405-1.405A2.032 2.032 0 0118 14.158V11a6.002 6.002 0 00-4-5.659V5a2 2 0 10-4 0v.341C7.67 6.165 6 8.388 6 11v3.159c0 .538-.214 1.055-.595 1.436L4 17h5m6 0v1a3 3 0 11-6 0v-1m6 0H9" />
                            </svg>
                        </div>
                        <h3 class="font-semibold text-gray-900 mb-2">Medication Reminders</h3>
                        <p class="text-sm text-gray-500 leading-relaxed">Set daily reminders for medications. Receive push notifications via the installable PWA.</p>
                    </div>

                    <!-- Role-Based Access -->
                    <div class="bg-gray-50 rounded-2xl p-6 border border-gray-100 hover:border-blue-200 transition-colors duration-200">
                        <div class="w-10 h-10 bg-blue-100 rounded-lg flex items-center justify-center mb-4">
                            <svg class="w-5 h-5 text-blue-800" fill="none" viewBox="0 0 24 24" stroke="currentColor">
                                <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M12 15v2m-6 4h12a2 2 0 002-2v-6a2 2 0 00-2-2H6a2 2 0 00-2 2v6a2 2 0 002 2zm10-10V7a4 4 0 00-8 0v4h8z" />
                            </svg>
                        </div>
                        <h3 class="font-semibold text-gray-900 mb-2">Role-Based Access</h3>
                        <p class="text-sm text-gray-500 leading-relaxed">Four distinct roles: Patient, Doctor, Staff, and Admin, each with tailored permissions and views.</p>
                    </div>

                    <!-- Encryption -->
                    <div class="bg-gray-50 rounded-2xl p-6 border border-gray-100 hover:border-blue-200 transition-colors duration-200">
                        <div class="w-10 h-10 bg-blue-100 rounded-lg flex items-center justify-center mb-4">
                            <svg class="w-5 h-5 text-blue-800" fill="none" viewBox="0 0 24 24" stroke="currentColor">
                                <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M9 12l2 2 4-4m5.618-4.016A11.955 11.955 0 0112 2.944a11.955 11.955 0 01-8.618 3.04A12.02 12.02 0 003 9c0 5.591 3.824 10.29 9 11.622 5.176-1.332 9-6.03 9-11.622 0-1.042-.133-2.052-.382-3.016z" />
                            </svg>
                        </div>
                        <h3 class="font-semibold text-gray-900 mb-2">AES-256 Encryption</h3>
                        <p class="text-sm text-gray-500 leading-relaxed">Sensitive data like insurance numbers and policy details are encrypted at rest using AES-256.</p>
                    </div>

                    <!-- Audit Logs -->
                    <div class="bg-gray-50 rounded-2xl p-6 border border-gray-100 hover:border-blue-200 transition-colors duration-200">
                        <div class="w-10 h-10 bg-blue-100 rounded-lg flex items-center justify-center mb-4">
                            <svg class="w-5 h-5 text-blue-800" fill="none" viewBox="0 0 24 24" stroke="currentColor">
                                <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M9 5H7a2 2 0 00-2 2v12a2 2 0 002 2h10a2 2 0 002-2V7a2 2 0 00-2-2h-2M9 5a2 2 0 002 2h2a2 2 0 002-2M9 5a2 2 0 012-2h2a2 2 0 012 2m-3 7h3m-3 4h3m-6-4h.01M9 16h.01" />
                            </svg>
                        </div>
                        <h3 class="font-semibold text-gray-900 mb-2">Complete Audit Trail</h3>
                        <p class="text-sm text-gray-500 leading-relaxed">Every action is logged: logins, record views, file downloads, for full GDPR accountability.</p>
                    </div>
                </div>
            </div>
        </section>

        <!-- Trust Bar -->
        <section class="py-10 px-6 bg-gray-50 border-t border-gray-100">
            <div class="max-w-4xl mx-auto flex flex-col sm:flex-row items-center justify-center gap-6 sm:gap-12 text-sm text-gray-500">
                <div class="flex items-center gap-2">
                    <svg class="w-4 h-4 text-green-600 shrink-0" fill="none" viewBox="0 0 24 24" stroke="currentColor">
                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M5 13l4 4L19 7" />
                    </svg>
                    <span>GDPR Compliant</span>
                </div>
                <div class="flex items-center gap-2">
                    <svg class="w-4 h-4 text-green-600 shrink-0" fill="none" viewBox="0 0 24 24" stroke="currentColor">
                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M5 13l4 4L19 7" />
                    </svg>
                    <span>Two-Factor Authentication</span>
                </div>
                <div class="flex items-center gap-2">
                    <svg class="w-4 h-4 text-green-600 shrink-0" fill="none" viewBox="0 0 24 24" stroke="currentColor">
                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M5 13l4 4L19 7" />
                    </svg>
                    <span>Installable PWA</span>
                </div>
            </div>
        </section>

        <!-- Footer -->
        <footer class="border-t border-gray-100 py-6 text-center text-xs text-gray-400">
            CardioSecure - Secure Cardiology Clinic Information System &middot; Healthcare Information Systems Security &middot; University of Padova
        </footer>
    </div>
</template>
