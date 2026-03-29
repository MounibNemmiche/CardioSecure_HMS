<script setup lang="ts">
import { useForm } from '@inertiajs/vue3'
import { ref } from 'vue'

const useRecoveryCode = ref(false)

const form = useForm({
    code: '',
    recovery_code: '',
})

function submit() {
    form.post('/two-factor-challenge', {
        onFinish: () => form.reset('code', 'recovery_code'),
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
                            d="M12 15v2m-6 4h12a2 2 0 002-2v-6a2 2 0 00-2-2H6a2 2 0 00-2 2v6a2 2 0 002 2zm10-10V7a4 4 0 00-8 0v4h8z" />
                    </svg>
                </div>
            </div>
            <h2 class="mt-4 text-center text-2xl font-bold text-gray-900">Two-factor authentication</h2>
            <p class="mt-1 text-center text-sm text-gray-500">
                <span v-if="!useRecoveryCode">Enter the code from your authenticator app</span>
                <span v-else>Enter one of your recovery codes</span>
            </p>
        </div>

        <div class="mt-8 sm:mx-auto sm:w-full sm:max-w-md">
            <div class="bg-white py-8 px-6 shadow-sm rounded-xl border border-gray-200">
                <form @submit.prevent="submit" class="space-y-5">
                    <div v-if="!useRecoveryCode">
                        <label for="code" class="block text-sm font-medium text-gray-700">Authentication code</label>
                        <input
                            id="code"
                            v-model="form.code"
                            type="text"
                            inputmode="numeric"
                            autocomplete="one-time-code"
                            autofocus
                            maxlength="6"
                            placeholder="000000"
                            class="mt-1 block w-full rounded-lg border border-gray-300 px-3 py-3 text-center text-2xl tracking-widest shadow-sm focus:border-blue-500 focus:outline-none focus:ring-1 focus:ring-blue-500"
                            :class="{ 'border-red-500': form.errors.code }"
                        />
                        <p v-if="form.errors.code" class="mt-1 text-xs text-red-600">{{ form.errors.code }}</p>
                    </div>

                    <div v-else>
                        <label for="recovery_code" class="block text-sm font-medium text-gray-700">Recovery code</label>
                        <input
                            id="recovery_code"
                            v-model="form.recovery_code"
                            type="text"
                            autocomplete="one-time-code"
                            autofocus
                            placeholder="xxxx-xxxx-xxxx-xxxx"
                            class="mt-1 block w-full rounded-lg border border-gray-300 px-3 py-2 text-sm font-mono shadow-sm focus:border-blue-500 focus:outline-none focus:ring-1 focus:ring-blue-500"
                            :class="{ 'border-red-500': form.errors.recovery_code }"
                        />
                        <p v-if="form.errors.recovery_code" class="mt-1 text-xs text-red-600">{{ form.errors.recovery_code }}</p>
                    </div>

                    <button
                        type="submit"
                        :disabled="form.processing"
                        class="w-full flex justify-center py-2.5 px-4 border border-transparent rounded-lg shadow-sm text-sm font-semibold text-white bg-blue-800 hover:bg-blue-900 focus:outline-none focus:ring-2 focus:ring-offset-2 focus:ring-blue-500 disabled:opacity-50 disabled:cursor-not-allowed transition"
                    >
                        <span v-if="form.processing">Verifying...</span>
                        <span v-else>Verify</span>
                    </button>
                </form>

                <button
                    type="button"
                    class="mt-4 w-full text-center text-sm text-blue-700 hover:text-blue-800 font-medium"
                    @click="useRecoveryCode = !useRecoveryCode; form.reset()"
                >
                    <span v-if="!useRecoveryCode">Use a recovery code</span>
                    <span v-else>Use authenticator app</span>
                </button>
            </div>
        </div>
    </div>
</template>
