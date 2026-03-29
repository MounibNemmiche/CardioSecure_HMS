<script setup lang="ts">
import StatCard from '@/components/StatCard.vue'
import AdminLayout from '@/layouts/AdminLayout.vue'
import { usePage } from '@inertiajs/vue3'

defineOptions({ layout: AdminLayout })

const page = usePage()
const userName = (page.props.auth as { user: { name: string } }).user.name.split(' ')[0]

const props = defineProps<{
    totalUsers: number
    totalDoctors: number
    totalPatients: number
    totalStaff: number
    recentLogs: {
        id: number
        action: string
        user: string
        ip: string | null
        created_at: string
    }[]
}>()
</script>

<template>
    <div>
        <h1 class="text-2xl font-bold text-gray-900 mb-1">Welcome back, {{ userName }}</h1>
        <p class="text-sm text-gray-500 mb-8">System administration and user management</p>

        <div class="grid grid-cols-1 sm:grid-cols-2 lg:grid-cols-4 gap-4 mb-8">
            <StatCard label="Total Users" :value="totalUsers" color="blue">
                <template #icon>
                    <svg class="w-5 h-5" fill="none" viewBox="0 0 24 24" stroke="currentColor">
                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M12 4.354a4 4 0 110 5.292M15 21H3v-1a6 6 0 0112 0v1zm0 0h6v-1a6 6 0 00-9-5.197M13 7a4 4 0 11-8 0 4 4 0 018 0z" />
                    </svg>
                </template>
            </StatCard>
            <StatCard label="Doctors" :value="totalDoctors" color="emerald">
                <template #icon>
                    <svg class="w-5 h-5" fill="none" viewBox="0 0 24 24" stroke="currentColor">
                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M4.318 6.318a4.5 4.5 0 000 6.364L12 20.364l7.682-7.682a4.5 4.5 0 00-6.364-6.364L12 7.636l-1.318-1.318a4.5 4.5 0 00-6.364 0z" />
                    </svg>
                </template>
            </StatCard>
            <StatCard label="Patients" :value="totalPatients" color="amber">
                <template #icon>
                    <svg class="w-5 h-5" fill="none" viewBox="0 0 24 24" stroke="currentColor">
                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M17 20h5v-2a3 3 0 00-5.356-1.857M17 20H7m10 0v-2c0-.656-.126-1.283-.356-1.857M7 20H2v-2a3 3 0 015.356-1.857M7 20v-2c0-.656.126-1.283.356-1.857m0 0a5.002 5.002 0 019.288 0M15 7a3 3 0 11-6 0 3 3 0 016 0z" />
                    </svg>
                </template>
            </StatCard>
            <StatCard label="Staff" :value="totalStaff" color="violet">
                <template #icon>
                    <svg class="w-5 h-5" fill="none" viewBox="0 0 24 24" stroke="currentColor">
                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M19 21V5a2 2 0 00-2-2H7a2 2 0 00-2 2v16m14 0h2m-2 0h-5m-9 0H3m2 0h5M9 7h1m-1 4h1m4-4h1m-1 4h1m-5 10v-5a1 1 0 011-1h2a1 1 0 011 1v5m-4 0h4" />
                    </svg>
                </template>
            </StatCard>
        </div>

        <div v-if="recentLogs.length > 0">
            <h2 class="text-lg font-semibold text-gray-900 mb-3">Recent Activity</h2>
            <div class="bg-white rounded-xl border border-gray-200 overflow-hidden">
                <table class="min-w-full text-sm">
                    <thead class="bg-gray-50 text-left">
                        <tr>
                            <th class="px-4 py-3 font-medium text-gray-500">Action</th>
                            <th class="px-4 py-3 font-medium text-gray-500">User</th>
                            <th class="px-4 py-3 font-medium text-gray-500">IP</th>
                        </tr>
                    </thead>
                    <tbody class="divide-y divide-gray-100">
                        <tr v-for="log in recentLogs" :key="log.id" class="hover:bg-gray-50 transition-colors duration-150">
                            <td class="px-4 py-3 text-gray-900 capitalize">{{ log.action.replace(/_/g, ' ') }}</td>
                            <td class="px-4 py-3 text-gray-600">{{ log.user }}</td>
                            <td class="px-4 py-3 text-gray-400 font-mono text-xs">{{ log.ip ?? '—' }}</td>
                        </tr>
                    </tbody>
                </table>
            </div>
        </div>
        <div v-else class="bg-white rounded-xl border border-gray-200 p-8 text-center">
            <p class="text-sm text-gray-400">No recent activity logged.</p>
        </div>
    </div>
</template>
