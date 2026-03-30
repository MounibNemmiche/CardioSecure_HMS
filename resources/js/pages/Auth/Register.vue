<script setup lang="ts">
import { useForm } from '@inertiajs/vue3'
import { ref } from 'vue'

const form = useForm({
    name: '',
    email: '',
    password: '',
    password_confirmation: '',
    phone: '',
    date_of_birth: '',
    codice_fiscale: '',
})

const showPassword = ref(false)

function submit() {
    form.post('/register', {
        onFinish: () => form.reset('password', 'password_confirmation'),
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
                <h1 class="text-3xl font-bold leading-tight mb-4">Join your<br />cardiology clinic</h1>
                <p class="text-blue-200 leading-relaxed max-w-sm mb-8">
                    Create your patient account to book appointments, access your medical records, and receive medication reminders.
                </p>
                <div class="space-y-3 text-sm text-blue-200">
                    <div class="flex items-center gap-2">
                        <svg class="w-4 h-4 text-blue-300 shrink-0" fill="none" viewBox="0 0 24 24" stroke="currentColor">
                            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M5 13l4 4L19 7" />
                        </svg>
                        <span>Book appointments online</span>
                    </div>
                    <div class="flex items-center gap-2">
                        <svg class="w-4 h-4 text-blue-300 shrink-0" fill="none" viewBox="0 0 24 24" stroke="currentColor">
                            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M5 13l4 4L19 7" />
                        </svg>
                        <span>View your medical records</span>
                    </div>
                    <div class="flex items-center gap-2">
                        <svg class="w-4 h-4 text-blue-300 shrink-0" fill="none" viewBox="0 0 24 24" stroke="currentColor">
                            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M5 13l4 4L19 7" />
                        </svg>
                        <span>Get medication push reminders</span>
                    </div>
                </div>
            </div>
            <p class="text-xs text-blue-300">University of Padova &middot; Secure Digital Healthcare</p>
        </div>

        <!-- Right form panel -->
        <div class="flex-1 flex flex-col justify-center py-12 px-6 sm:px-12 bg-gray-50">
            <div class="w-full max-w-lg mx-auto">
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

                <h2 class="text-2xl font-bold text-gray-900 mb-1">Create patient account</h2>
                <p class="text-sm text-gray-500 mb-8">Register to access CardioSecure</p>

                <div class="bg-white py-8 px-6 shadow-sm rounded-xl border border-gray-200">
                    <form @submit.prevent="submit" class="space-y-5">
                        <div>
                            <label for="name" class="block text-sm font-medium text-gray-700">Full name</label>
                            <input
                                id="name"
                                v-model="form.name"
                                type="text"
                                autocomplete="name"
                                required
                                class="mt-1 block w-full rounded-lg border border-gray-300 px-3 py-2.5 text-sm shadow-sm transition-colors duration-200 focus:border-blue-500 focus:outline-none focus:ring-2 focus:ring-blue-500/20"
                                :class="{ 'border-red-500 focus:border-red-500 focus:ring-red-500/20': form.errors.name }"
                            />
                            <p v-if="form.errors.name" class="mt-1.5 text-xs text-red-600">{{ form.errors.name }}</p>
                        </div>

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

                        <div class="grid grid-cols-1 gap-5 sm:grid-cols-2">
                            <div>
                                <label for="phone" class="block text-sm font-medium text-gray-700">Phone number</label>
                                <input
                                    id="phone"
                                    v-model="form.phone"
                                    type="tel"
                                    autocomplete="tel"
                                    required
                                    placeholder="+39 333 000 0000"
                                    class="mt-1 block w-full rounded-lg border border-gray-300 px-3 py-2.5 text-sm shadow-sm transition-colors duration-200 focus:border-blue-500 focus:outline-none focus:ring-2 focus:ring-blue-500/20"
                                    :class="{ 'border-red-500 focus:border-red-500 focus:ring-red-500/20': form.errors.phone }"
                                />
                                <p v-if="form.errors.phone" class="mt-1.5 text-xs text-red-600">{{ form.errors.phone }}</p>
                            </div>

                            <div>
                                <label for="date_of_birth" class="block text-sm font-medium text-gray-700">Date of birth</label>
                                <input
                                    id="date_of_birth"
                                    v-model="form.date_of_birth"
                                    type="date"
                                    required
                                    class="mt-1 block w-full rounded-lg border border-gray-300 px-3 py-2.5 text-sm shadow-sm transition-colors duration-200 focus:border-blue-500 focus:outline-none focus:ring-2 focus:ring-blue-500/20"
                                    :class="{ 'border-red-500 focus:border-red-500 focus:ring-red-500/20': form.errors.date_of_birth }"
                                />
                                <p v-if="form.errors.date_of_birth" class="mt-1.5 text-xs text-red-600">{{ form.errors.date_of_birth }}</p>
                            </div>
                        </div>

                        <div>
                            <label for="codice_fiscale" class="block text-sm font-medium text-gray-700">Codice Fiscale (Italian tax code)</label>
                            <input
                                id="codice_fiscale"
                                v-model="form.codice_fiscale"
                                type="text"
                                maxlength="16"
                                required
                                placeholder="RSSMRA85C55H501Z"
                                class="mt-1 block w-full rounded-lg border border-gray-300 px-3 py-2.5 text-sm uppercase tracking-wider shadow-sm transition-colors duration-200 focus:border-blue-500 focus:outline-none focus:ring-2 focus:ring-blue-500/20"
                                :class="{ 'border-red-500 focus:border-red-500 focus:ring-red-500/20': form.errors.codice_fiscale }"
                            />
                            <p v-if="form.errors.codice_fiscale" class="mt-1.5 text-xs text-red-600">{{ form.errors.codice_fiscale }}</p>
                        </div>

                        <div>
                            <label for="password" class="block text-sm font-medium text-gray-700">Password</label>
                            <div class="relative mt-1">
                                <input
                                    id="password"
                                    v-model="form.password"
                                    :type="showPassword ? 'text' : 'password'"
                                    autocomplete="new-password"
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
                            <p class="mt-1.5 text-xs text-gray-400">Min. 8 characters with uppercase, numbers and symbols.</p>
                        </div>

                        <div>
                            <label for="password_confirmation" class="block text-sm font-medium text-gray-700">Confirm password</label>
                            <input
                                id="password_confirmation"
                                v-model="form.password_confirmation"
                                :type="showPassword ? 'text' : 'password'"
                                autocomplete="new-password"
                                required
                                class="mt-1 block w-full rounded-lg border border-gray-300 px-3 py-2.5 text-sm shadow-sm transition-colors duration-200 focus:border-blue-500 focus:outline-none focus:ring-2 focus:ring-blue-500/20"
                            />
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
                            <span v-if="form.processing">Creating account...</span>
                            <span v-else>Create account</span>
                        </button>
                    </form>

                    <p class="mt-6 text-center text-sm text-gray-500">
                        Already have an account?
                        <a href="/login" class="font-medium text-blue-700 hover:text-blue-800 transition-colors duration-200">Sign in</a>
                    </p>
                </div>
            </div>
        </div>
    </div>
</template>
