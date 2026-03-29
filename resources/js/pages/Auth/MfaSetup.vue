<script setup lang="ts">
import { useForm, router } from '@inertiajs/vue3'
import { ref } from 'vue'

const step = ref<'enable' | 'qr' | 'confirm' | 'recovery'>('enable')
const qrCodeSvg = ref('')
const recoveryCodes = ref<string[]>([])
const setupSecret = ref('')
const enableError = ref('')

const confirmForm = useForm({ code: '' })

function getCsrfToken(): string {
    const cookie = document.cookie
        .split('; ')
        .find(row => row.startsWith('XSRF-TOKEN='))
    return cookie ? decodeURIComponent(cookie.split('=')[1]) : ''
}

async function enableTwoFactor() {
    enableError.value = ''
    try {
        const res = await fetch('/user/two-factor-authentication', {
            method: 'POST',
            headers: {
                'X-XSRF-TOKEN': getCsrfToken(),
                'Content-Type': 'application/json',
                'Accept': 'application/json',
            },
        })

        if (!res.ok && res.status !== 200) {
            enableError.value = `Failed to enable 2FA (status ${res.status}). Please try again.`
            return
        }

        await loadQrCode()
        step.value = 'qr'
    } catch (e) {
        enableError.value = 'Network error. Please refresh and try again.'
    }
}

async function loadQrCode() {
    const [qrRes, secretRes] = await Promise.all([
        fetch('/user/two-factor-qr-code', {
            headers: {
                'Accept': 'application/json',
                'X-XSRF-TOKEN': getCsrfToken(),
            },
        }),
        fetch('/user/two-factor-secret-key', {
            headers: {
                'Accept': 'application/json',
                'X-XSRF-TOKEN': getCsrfToken(),
            },
        }),
    ])

    if (qrRes.ok) {
        const qrData = await qrRes.json()
        qrCodeSvg.value = qrData.svg ?? ''
    }

    if (secretRes.ok) {
        const secretData = await secretRes.json()
        setupSecret.value = secretData.secretKey ?? ''
    }
}

function confirmCode() {
    confirmForm.post('/user/confirmed-two-factor-authentication', {
        onSuccess: async () => {
            const res = await fetch('/user/two-factor-recovery-codes', {
                headers: {
                    'Accept': 'application/json',
                    'X-XSRF-TOKEN': getCsrfToken(),
                },
            })
            if (res.ok) {
                recoveryCodes.value = await res.json()
            }
            step.value = 'recovery'
        },
        onError: () => {},
    })
}

function finish() {
    router.visit('/dashboard')
}
</script>

<template>
    <div class="min-h-screen bg-gray-50 flex flex-col justify-center py-12 sm:px-6 lg:px-8">
        <div class="sm:mx-auto sm:w-full sm:max-w-md">
            <div class="flex justify-center">
                <div class="w-16 h-16 bg-blue-800 rounded-2xl flex items-center justify-center">
                    <svg class="w-10 h-10 text-white" fill="none" viewBox="0 0 24 24" stroke="currentColor">
                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2"
                            d="M9 12l2 2 4-4m5.618-4.016A11.955 11.955 0 0112 2.944a11.955 11.955 0 01-8.618 3.04A12.02 12.02 0 003 9c0 5.591 3.824 10.29 9 11.622 5.176-1.332 9-6.03 9-11.622 0-1.042-.133-2.052-.382-3.016z" />
                    </svg>
                </div>
            </div>
            <h2 class="mt-4 text-center text-2xl font-bold text-gray-900">Set up two-factor authentication</h2>
            <p class="mt-1 text-center text-sm text-gray-500">Required to access CardioSecure</p>
        </div>

        <!-- Progress steps -->
        <div class="mt-6 sm:mx-auto sm:w-full sm:max-w-md">
            <div class="flex items-center justify-center gap-2 text-xs text-gray-400">
                <span :class="step === 'enable' ? 'text-blue-700 font-semibold' : 'text-gray-400'">1. Enable</span>
                <span class="text-gray-300">&rsaquo;</span>
                <span :class="step === 'qr' ? 'text-blue-700 font-semibold' : 'text-gray-400'">2. Scan QR</span>
                <span class="text-gray-300">&rsaquo;</span>
                <span :class="step === 'confirm' ? 'text-blue-700 font-semibold' : 'text-gray-400'">3. Verify</span>
                <span class="text-gray-300">&rsaquo;</span>
                <span :class="step === 'recovery' ? 'text-blue-700 font-semibold' : 'text-gray-400'">4. Recovery codes</span>
            </div>
        </div>

        <div class="mt-4 sm:mx-auto sm:w-full sm:max-w-md">
            <div class="bg-white py-8 px-6 shadow-sm rounded-xl border border-gray-200">

                <!-- Step 1: Enable -->
                <div v-if="step === 'enable'" class="text-center">
                    <div class="mb-6">
                        <div class="mx-auto w-16 h-16 bg-blue-50 rounded-full flex items-center justify-center mb-4">
                            <svg class="w-8 h-8 text-blue-700" fill="none" viewBox="0 0 24 24" stroke="currentColor">
                                <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2"
                                    d="M12 18h.01M8 21h8a2 2 0 002-2V5a2 2 0 00-2-2H8a2 2 0 00-2 2v14a2 2 0 002 2z" />
                            </svg>
                        </div>
                        <p class="text-sm text-gray-600">
                            To protect patient health data, CardioSecure requires two-factor authentication (2FA).<br /><br />
                            You will need an authenticator app such as <strong>Google Authenticator</strong>, <strong>Microsoft Authenticator</strong>, or <strong>Authy</strong>.
                        </p>
                    </div>
                    <div v-if="enableError" class="mb-4 rounded-lg bg-red-50 border border-red-200 p-3 text-sm text-red-700">
                        {{ enableError }}
                    </div>
                    <button
                        class="w-full flex justify-center py-2.5 px-4 border border-transparent rounded-lg shadow-sm text-sm font-semibold text-white bg-blue-800 hover:bg-blue-900 focus:outline-none focus:ring-2 focus:ring-offset-2 focus:ring-blue-500 transition"
                        @click="enableTwoFactor"
                    >
                        Set up 2FA
                    </button>
                </div>

                <!-- Step 2: QR Code -->
                <div v-else-if="step === 'qr'">
                    <p class="text-sm text-gray-600 mb-4 text-center">
                        Open your authenticator app and scan the QR code below, then click <strong>Next</strong>.
                    </p>

                    <div v-if="qrCodeSvg" class="flex justify-center mb-4 p-4 bg-white border border-gray-200 rounded-lg" v-html="qrCodeSvg"></div>
                    <div v-else class="flex justify-center mb-4 p-8 bg-gray-50 border border-gray-200 rounded-lg">
                        <p class="text-sm text-gray-400">Loading QR code...</p>
                    </div>

                    <div v-if="setupSecret" class="mb-5 p-3 bg-gray-50 rounded-lg text-center">
                        <p class="text-xs text-gray-500 mb-1">Can't scan? Enter this code manually in your app:</p>
                        <code class="text-sm font-mono font-bold text-gray-800 tracking-widest break-all">{{ setupSecret }}</code>
                    </div>

                    <button
                        class="w-full flex justify-center py-2.5 px-4 border border-transparent rounded-lg shadow-sm text-sm font-semibold text-white bg-blue-800 hover:bg-blue-900 transition"
                        @click="step = 'confirm'"
                    >
                        Next &rarr;
                    </button>
                </div>

                <!-- Step 3: Confirm code -->
                <div v-else-if="step === 'confirm'">
                    <p class="text-sm text-gray-600 mb-4 text-center">
                        Enter the 6-digit code shown in your authenticator app to confirm setup.
                    </p>
                    <form @submit.prevent="confirmCode" class="space-y-4">
                        <input
                            v-model="confirmForm.code"
                            type="text"
                            inputmode="numeric"
                            maxlength="6"
                            placeholder="000000"
                            autofocus
                            class="block w-full rounded-lg border border-gray-300 px-3 py-3 text-center text-2xl tracking-widest shadow-sm focus:border-blue-500 focus:outline-none focus:ring-1 focus:ring-blue-500"
                            :class="{ 'border-red-500': confirmForm.errors.code }"
                        />
                        <p v-if="confirmForm.errors.code" class="text-xs text-red-600 text-center">{{ confirmForm.errors.code }}</p>
                        <div class="flex gap-3">
                            <button
                                type="button"
                                class="flex-1 py-2.5 px-4 border border-gray-300 rounded-lg text-sm font-semibold text-gray-700 hover:bg-gray-50 transition"
                                @click="step = 'qr'"
                            >
                                &larr; Back
                            </button>
                            <button
                                type="submit"
                                :disabled="confirmForm.processing || confirmForm.code.length < 6"
                                class="flex-1 flex justify-center py-2.5 px-4 border border-transparent rounded-lg shadow-sm text-sm font-semibold text-white bg-blue-800 hover:bg-blue-900 focus:outline-none focus:ring-2 focus:ring-offset-2 focus:ring-blue-500 disabled:opacity-50 transition"
                            >
                                <span v-if="confirmForm.processing">Verifying...</span>
                                <span v-else>Verify</span>
                            </button>
                        </div>
                    </form>
                </div>

                <!-- Step 4: Recovery codes -->
                <div v-else-if="step === 'recovery'">
                    <div class="mb-4 rounded-lg bg-amber-50 border border-amber-200 p-3">
                        <p class="text-xs text-amber-800 font-semibold mb-1">Save these recovery codes</p>
                        <p class="text-xs text-amber-700">Store them somewhere safe. Use them to access your account if you lose access to your authenticator app. Each code can only be used once.</p>
                    </div>
                    <div class="bg-gray-50 rounded-lg p-4 mb-4">
                        <div class="grid grid-cols-2 gap-2">
                            <code
                                v-for="code in recoveryCodes"
                                :key="code"
                                class="text-xs font-mono text-gray-700 bg-white border border-gray-200 rounded px-2 py-1 text-center"
                            >{{ code }}</code>
                        </div>
                    </div>
                    <button
                        class="w-full flex justify-center py-2.5 px-4 border border-transparent rounded-lg shadow-sm text-sm font-semibold text-white bg-blue-800 hover:bg-blue-900 transition"
                        @click="finish"
                    >
                        I've saved the codes &mdash; Continue
                    </button>
                </div>

            </div>
        </div>
    </div>
</template>
