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
    { name: 'Dashboard', href: '/admin/dashboard', icon: 'M3 12l2-2m0 0l7-7 7 7M5 10v10a1 1 0 001 1h3m10-11l2 2m-2-2v10a1 1 0 01-1 1h-3m-6 0a1 1 0 001-1v-4a1 1 0 011-1h2a1 1 0 011 1v4a1 1 0 001 1m-6 0h6' },
    { name: 'Users', href: '/admin/users', icon: 'M12 4.354a4 4 0 110 5.292M15 21H3v-1a6 6 0 0112 0v1zm0 0h6v-1a6 6 0 00-9-5.197M13 7a4 4 0 11-8 0 4 4 0 018 0z' },
    { name: 'Insurance Plans', href: '/admin/insurance-plans', icon: 'M9 12l2 2 4-4m5.618-4.016A11.955 11.955 0 0112 2.944a11.955 11.955 0 01-8.618 3.04A12.02 12.02 0 003 9c0 5.591 3.824 10.29 9 11.622 5.176-1.332 9-6.03 9-11.622 0-1.042-.133-2.052-.382-3.016z' },
    { name: 'Audit Logs', href: '/admin/audit-logs', icon: 'M9 5H7a2 2 0 00-2 2v12a2 2 0 002 2h10a2 2 0 002-2V7a2 2 0 00-2-2h-2M9 5a2 2 0 002 2h2a2 2 0 002-2M9 5a2 2 0 012-2h2a2 2 0 012 2' },
    { name: 'Profile', href: '/admin/profile', icon: 'M16 7a4 4 0 11-8 0 4 4 0 018 0zM12 14a7 7 0 00-7 7h14a7 7 0 00-7-7z' },
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
                    <p class="text-xs text-red-600 font-medium">Admin</p>
                </div>
            </div>

            <nav class="flex-1 px-3 py-4 space-y-1">
                <a
                    v-for="link in navLinks"
                    :key="link.name"
                    :href="link.href"
                    class="flex items-center gap-3 px-3 py-2 rounded-lg text-sm font-medium transition-colors duration-200 cursor-pointer"
                    :class="$page.url.startsWith(link.href)
                        ? 'bg-red-50 text-red-800'
                        : 'text-gray-600 hover:bg-gray-50 hover:text-gray-900'"
                >
                    <svg class="w-4 h-4 shrink-0" fill="none" viewBox="0 0 24 24" stroke="currentColor">
                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" :d="link.icon" />
                    </svg>
                    {{ link.name }}
                </a>
            </nav>

            <div class="px-4 py-4 border-t border-gray-100">
                <p class="text-xs font-medium text-gray-900 truncate">{{ auth.user.name }}</p>
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
