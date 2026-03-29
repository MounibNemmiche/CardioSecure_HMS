<script setup lang="ts">
import { useForm } from '@inertiajs/vue3'
import { ref } from 'vue'

const resent = ref(false)

const form = useForm({})

function resend() {
    form.post('/email/verification-notification', {
        onSuccess: () => { resent.value = true },
    })
}
</script>

<template>
    <div class="min-h-screen bg-gray-50 flex flex-col justify-center py-12 sm:px-6 lg:px-8">
        <div class="sm:mx-auto sm:w-full sm:max-w-md">
            <div class="flex justify-center">
                <div class="w-16 h-16 bg-blue-800 rounded-2xl flex items-center justify-center">
                    <svg class="w-10 h-10 text-white" fill="none" viewBox="0 0 24 24" stroke="currentColor">
                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2"
                            d="M3 8l7.89 5.26a2 2 0 002.22 0L21 8M5 19h14a2 2 0 002-2V7a2 2 0 00-2-2H5a2 2 0 00-2 2v10a2 2 0 002 2z" />
                    </svg>
                </div>
            </div>
            <h2 class="mt-4 text-center text-2xl font-bold text-gray-900">Verify your email</h2>
        </div>

        <div class="mt-8 sm:mx-auto sm:w-full sm:max-w-md">
            <div class="bg-white py-8 px-6 shadow-sm rounded-xl border border-gray-200 text-center">
                <p class="text-sm text-gray-600 mb-6">
                    We sent a verification link to your email address. Click the link to activate your CardioSecure account.
                </p>

                <div v-if="resent" class="mb-4 rounded-lg bg-green-50 border border-green-200 p-3 text-sm text-green-700">
                    Verification email resent successfully.
                </div>

                <button
                    :disabled="form.processing"
                    class="w-full flex justify-center py-2.5 px-4 border border-transparent rounded-lg shadow-sm text-sm font-semibold text-white bg-blue-800 hover:bg-blue-900 focus:outline-none focus:ring-2 focus:ring-offset-2 focus:ring-blue-500 disabled:opacity-50 disabled:cursor-not-allowed transition"
                    @click="resend"
                >
                    <span v-if="form.processing">Sending...</span>
                    <span v-else>Resend verification email</span>
                </button>

                <form method="POST" action="/logout" class="mt-4">
                    <input type="hidden" name="_token" :value="$page.props.csrf_token" />
                    <button type="submit" class="text-sm text-gray-500 hover:text-gray-700">
                        Sign out
                    </button>
                </form>
            </div>
        </div>
    </div>
</template>
