<script setup lang="ts">
import { useForm } from '@inertiajs/vue3'
import { ref } from 'vue'

const sent = ref(false)

const form = useForm({
    email: '',
})

function submit() {
    form.post('/forgot-password', {
        onSuccess: () => { sent.value = true },
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
                            d="M15 7a2 2 0 012 2m4 0a6 6 0 01-7.743 5.743L11 17H9v2H7v2H4a1 1 0 01-1-1v-2.586a1 1 0 01.293-.707l5.964-5.964A6 6 0 1121 9z" />
                    </svg>
                </div>
            </div>
            <h2 class="mt-4 text-center text-2xl font-bold text-gray-900">Reset password</h2>
            <p class="mt-1 text-center text-sm text-gray-500">Enter your email to receive reset instructions</p>
        </div>

        <div class="mt-8 sm:mx-auto sm:w-full sm:max-w-md">
            <div class="bg-white py-8 px-6 shadow-sm rounded-xl border border-gray-200">
                <div v-if="sent" class="mb-5 rounded-lg bg-green-50 border border-green-200 p-4 text-sm text-green-700">
                    Password reset instructions have been sent to your email address.
                </div>

                <form v-if="!sent" @submit.prevent="submit" class="space-y-5">
                    <div>
                        <label for="email" class="block text-sm font-medium text-gray-700">Email</label>
                        <input
                            id="email"
                            v-model="form.email"
                            type="email"
                            autocomplete="email"
                            autofocus
                            required
                            class="mt-1 block w-full rounded-lg border border-gray-300 px-3 py-2 text-sm shadow-sm focus:border-blue-500 focus:outline-none focus:ring-1 focus:ring-blue-500"
                            :class="{ 'border-red-500': form.errors.email }"
                        />
                        <p v-if="form.errors.email" class="mt-1 text-xs text-red-600">{{ form.errors.email }}</p>
                    </div>

                    <button
                        type="submit"
                        :disabled="form.processing"
                        class="w-full flex justify-center py-2.5 px-4 border border-transparent rounded-lg shadow-sm text-sm font-semibold text-white bg-blue-800 hover:bg-blue-900 focus:outline-none focus:ring-2 focus:ring-offset-2 focus:ring-blue-500 disabled:opacity-50 disabled:cursor-not-allowed transition"
                    >
                        <span v-if="form.processing">Sending...</span>
                        <span v-else>Send reset instructions</span>
                    </button>
                </form>

                <p class="mt-6 text-center text-sm text-gray-500">
                    <a href="/login" class="font-medium text-blue-700 hover:text-blue-800">Back to login</a>
                </p>
            </div>
        </div>
    </div>
</template>
