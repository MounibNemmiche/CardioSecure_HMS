<script setup lang="ts">
import { router, usePage } from '@inertiajs/vue3'
import { ref } from 'vue'
import FlashMessage from '@/components/FlashMessage.vue'
import type { AuthUser } from '@/types'

const page = usePage()
const auth = page.props.auth as { user: AuthUser }
const sidebarOpen = ref(false)

function logout() {
    router.post('/logout')
}

const navLinks = [
    { name: 'Dashboard', href: '/doctor/dashboard', icon: 'M3 12l2-2m0 0l7-7 7 7M5 10v10a1 1 0 001 1h3m10-11l2 2m-2-2v10a1 1 0 01-1 1h-3m-6 0a1 1 0 001-1v-4a1 1 0 011-1h2a1 1 0 011 1v4a1 1 0 001 1m-6 0h6' },
    { name: 'My Schedule', href: '/doctor/appointments', icon: 'M8 7V3m8 4V3m-9 8h10M5 21h14a2 2 0 002-2V7a2 2 0 00-2-2H5a2 2 0 00-2 2v12a2 2 0 002 2z' },
    { name: 'My Patients', href: '/doctor/patients', icon: 'M17 20h5v-2a3 3 0 00-5.356-1.857M17 20H7m10 0v-2c0-.656-.126-1.283-.356-1.857M7 20H2v-2a3 3 0 015.356-1.857M7 20v-2c0-.656.126-1.283.356-1.857m0 0a5.002 5.002 0 019.288 0M15 7a3 3 0 11-6 0 3 3 0 016 0z' },
    { name: 'Profile', href: '/doctor/profile', icon: 'M16 7a4 4 0 11-8 0 4 4 0 018 0zM12 14a7 7 0 00-7 7h14a7 7 0 00-7-7z' },
]
</script>

<template>
    <div class="min-h-screen bg-gray-50 flex">
        <FlashMessage />

        <aside
            class="fixed inset-y-0 left-0 z-40 w-56 bg-white border-r border-gray-200 flex flex-col transition-transform duration-200"
            :class="sidebarOpen ? 'translate-x-0' : '-translate-x-full md:translate-x-0'"
        >
            <div class="flex items-center gap-2 px-5 py-4 border-b border-gray-100">
                <div class="w-8 h-8 bg-blue-800 rounded-lg flex items-center justify-center shrink-0">
                    <svg class="w-5 h-5 text-white" fill="none" viewBox="0 0 24 24" stroke="currentColor">
                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M4.318 6.318a4.5 4.5 0 000 6.364L12 20.364l7.682-7.682a4.5 4.5 0 00-6.364-6.364L12 7.636l-1.318-1.318a4.5 4.5 0 00-6.364 0z" />
                    </svg>
                </div>
                <div>
                    <p class="text-sm font-bold text-gray-900 leading-tight">CardioSecure</p>
                    <p class="text-xs text-emerald-700 font-medium">Doctor</p>
                </div>
            </div>

            <nav class="flex-1 px-3 py-4 space-y-1">
                <a
                    v-for="link in navLinks"
                    :key="link.name"
                    :href="link.href"
                    class="flex items-center gap-3 px-3 py-2 rounded-lg text-sm font-medium transition-colors duration-200 cursor-pointer"
                    :class="$page.url.startsWith(link.href)
                        ? 'bg-emerald-50 text-emerald-800'
                        : 'text-gray-600 hover:bg-gray-50 hover:text-gray-900'"
                >
                    <svg class="w-4 h-4 shrink-0" fill="none" viewBox="0 0 24 24" stroke="currentColor">
                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" :d="link.icon" />
                    </svg>
                    {{ link.name }}
                </a>
            </nav>

            <div class="px-4 py-4 border-t border-gray-100">
                <p class="text-xs font-medium text-gray-900 truncate">Dr. {{ auth.user.name }}</p>
                <p class="text-xs text-gray-400 truncate mb-2">{{ auth.user.email }}</p>
                <button class="w-full text-left text-xs text-gray-500 hover:text-red-600 transition-colors duration-200 cursor-pointer" @click="logout">
                    Sign out
                </button>
            </div>
        </aside>

        <div v-if="sidebarOpen" class="fixed inset-0 z-30 bg-black/30 md:hidden" @click="sidebarOpen = false" />

        <div class="flex-1 md:ml-56 flex flex-col min-h-screen">
            <div class="md:hidden flex items-center gap-3 px-4 py-3 bg-white border-b border-gray-200">
                <button class="text-gray-500 cursor-pointer hover:text-gray-700 transition-colors duration-200" @click="sidebarOpen = true">
                    <svg class="w-5 h-5" fill="none" viewBox="0 0 24 24" stroke="currentColor">
                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M4 6h16M4 12h16M4 18h16" />
                    </svg>
                </button>
                <span class="font-bold text-gray-900 text-sm">CardioSecure</span>
            </div>
            <main class="flex-1 px-6 py-8 max-w-5xl w-full mx-auto">
                <slot />
            </main>
        </div>
    </div>
</template>
