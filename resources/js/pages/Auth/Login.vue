<script setup lang="ts">
import { useForm } from '@inertiajs/vue3'
import { ref } from 'vue'

const form = useForm({
    email: '',
    password: '',
    remember: false,
})

const showPassword = ref(false)

function submit() {
    form.post('/login', {
        onFinish: () => form.reset('password'),
    })
}
</script>

<template>
    <div class="min-h-screen flex">
        <!-- Left branding panel (hidden on mobile) -->
        <div class="hidden lg:flex lg:w-2/5 bg-blue-800 flex-col justify-between p-10 text-white">
            <div>
                <div class="flex items-center gap-3 mb-16">
                    <div class="w-10 h-10 bg-white/15 rounded-xl flex items-center justify-center">
                        <svg class="w-6 h-6 text-white" fill="none" viewBox="0 0 24 24" stroke="currentColor">
                            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2"
                                d="M4.318 6.318a4.5 4.5 0 000 6.364L12 20.364l7.682-7.682a4.5 4.5 0 00-6.364-6.364L12 7.636l-1.318-1.318a4.5 4.5 0 00-6.364 0z" />
                        </svg>
                    </div>
                    <span class="text-lg font-bold">CardioSecure</span>
                </div>
                <h1 class="text-3xl font-bold leading-tight mb-4">Secure access to your<br />cardiology clinic</h1>
                <p class="text-blue-200 leading-relaxed max-w-sm">
                    Manage appointments, view medical records, and stay on top of medication reminders — all protected by two-factor authentication.
                </p>
            </div>
            <p class="text-xs text-blue-300">University of Padova &middot; Healthcare Information Systems Security</p>
        </div>

        <!-- Right form panel -->
        <div class="flex-1 flex flex-col justify-center py-12 px-6 sm:px-12 bg-gray-50">
            <div class="w-full max-w-md mx-auto">
                <!-- Mobile logo -->
                <div class="flex justify-center lg:hidden mb-8">
                    <div class="flex items-center gap-3">
                        <div class="w-12 h-12 bg-blue-800 rounded-xl flex items-center justify-center">
                            <svg class="w-7 h-7 text-white" fill="none" viewBox="0 0 24 24" stroke="currentColor">
                                <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2"
                                    d="M4.318 6.318a4.5 4.5 0 000 6.364L12 20.364l7.682-7.682a4.5 4.5 0 00-6.364-6.364L12 7.636l-1.318-1.318a4.5 4.5 0 00-6.364 0z" />
                            </svg>
                        </div>
                        <span class="text-xl font-bold text-gray-900">CardioSecure</span>
                    </div>
                </div>

                <h2 class="text-2xl font-bold text-gray-900 mb-1">Welcome back</h2>
                <p class="text-sm text-gray-500 mb-8">Sign in to your account to continue</p>

                <div class="bg-white py-8 px-6 shadow-sm rounded-xl border border-gray-200">
                    <form @submit.prevent="submit" class="space-y-5">
                        <div>
                            <label for="email" class="block text-sm font-medium text-gray-700">Email</label>
                            <input
                                id="email"
                                v-model="form.email"
                                type="email"
                                autocomplete="email"
                                required
                                class="mt-1 block w-full rounded-lg border border-gray-300 px-3 py-2.5 text-sm shadow-sm transition-colors duration-200 focus:border-blue-500 focus:outline-none focus:ring-2 focus:ring-blue-500/20"
                                :class="{ 'border-red-500 focus:border-red-500 focus:ring-red-500/20': form.errors.email }"
                            />
                            <p v-if="form.errors.email" class="mt-1.5 text-xs text-red-600">{{ form.errors.email }}</p>
                        </div>

                        <div>
                            <label for="password" class="block text-sm font-medium text-gray-700">Password</label>
                            <div class="relative mt-1">
                                <input
                                    id="password"
                                    v-model="form.password"
                                    :type="showPassword ? 'text' : 'password'"
                                    autocomplete="current-password"
                                    required
                                    class="block w-full rounded-lg border border-gray-300 px-3 py-2.5 pr-10 text-sm shadow-sm transition-colors duration-200 focus:border-blue-500 focus:outline-none focus:ring-2 focus:ring-blue-500/20"
                                    :class="{ 'border-red-500 focus:border-red-500 focus:ring-red-500/20': form.errors.password }"
                                />
                                <button
                                    type="button"
                                    class="absolute inset-y-0 right-0 flex items-center pr-3 text-gray-400 hover:text-gray-600 cursor-pointer transition-colors duration-200"
                                    @click="showPassword = !showPassword"
                                >
                                    <svg v-if="!showPassword" class="w-4 h-4" fill="none" viewBox="0 0 24 24" stroke="currentColor">
                                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M15 12a3 3 0 11-6 0 3 3 0 016 0z" />
                                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M2.458 12C3.732 7.943 7.523 5 12 5c4.478 0 8.268 2.943 9.542 7-1.274 4.057-5.064 7-9.542 7-4.477 0-8.268-2.943-9.542-7z" />
                                    </svg>
                                    <svg v-else class="w-4 h-4" fill="none" viewBox="0 0 24 24" stroke="currentColor">
                                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M13.875 18.825A10.05 10.05 0 0112 19c-4.478 0-8.268-2.943-9.543-7a9.97 9.97 0 011.563-3.029m5.858.908a3 3 0 114.243 4.243M9.878 9.878l4.242 4.242M9.88 9.88l-3.29-3.29m7.532 7.532l3.29 3.29M3 3l3.59 3.59m0 0A9.953 9.953 0 0112 5c4.478 0 8.268 2.943 9.543 7a10.025 10.025 0 01-4.132 5.411m0 0L21 21" />
                                    </svg>
                                </button>
                            </div>
                            <p v-if="form.errors.password" class="mt-1.5 text-xs text-red-600">{{ form.errors.password }}</p>
                        </div>

                        <div class="flex items-center justify-between">
                            <label class="flex items-center gap-2 text-sm text-gray-600 cursor-pointer">
                                <input v-model="form.remember" type="checkbox" class="rounded border-gray-300 text-blue-600 cursor-pointer focus:ring-blue-500" />
                                Remember me
                            </label>
                            <a href="/forgot-password" class="text-sm text-blue-700 hover:text-blue-800 font-medium transition-colors duration-200">
                                Forgot password?
                            </a>
                        </div>

                        <button
                            type="submit"
                            :disabled="form.processing"
                            class="w-full flex justify-center py-2.5 px-4 rounded-lg shadow-sm text-sm font-semibold text-white bg-blue-800 hover:bg-blue-900 focus:outline-none focus:ring-2 focus:ring-offset-2 focus:ring-blue-500 disabled:opacity-50 disabled:cursor-not-allowed transition-all duration-200 cursor-pointer"
                        >
                            <svg v-if="form.processing" class="animate-spin -ml-1 mr-2 h-4 w-4 text-white" fill="none" viewBox="0 0 24 24">
                                <circle class="opacity-25" cx="12" cy="12" r="10" stroke="currentColor" stroke-width="4"></circle>
                                <path class="opacity-75" fill="currentColor" d="M4 12a8 8 0 018-8V0C5.373 0 0 5.373 0 12h4zm2 5.291A7.962 7.962 0 014 12H0c0 3.042 1.135 5.824 3 7.938l3-2.647z"></path>
                            </svg>
                            <span v-if="form.processing">Signing in...</span>
                            <span v-else>Sign in</span>
                        </button>
                    </form>

                    <p class="mt-6 text-center text-sm text-gray-500">
                        Don't have an account?
                        <a href="/register" class="font-medium text-blue-700 hover:text-blue-800 transition-colors duration-200">Register as a patient</a>
                    </p>
                </div>
            </div>
        </div>
    </div>
</template>
