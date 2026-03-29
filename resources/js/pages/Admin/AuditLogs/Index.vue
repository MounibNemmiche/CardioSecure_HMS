<script setup lang="ts">
import { ref } from 'vue'
import { router } from '@inertiajs/vue3'
import AdminLayout from '@/layouts/AdminLayout.vue'
import type { AuditLog } from '@/types'

defineOptions({ layout: AdminLayout })

const props = defineProps<{
    logs: {
        data: AuditLog[]
        links: { url: string | null; label: string; active: boolean }[]
    }
    actions: string[]
    filters: {
        action?: string
        user_id?: string
        date_from?: string
        date_to?: string
    }
}>()

const filterAction = ref(props.filters.action || '')
const filterDateFrom = ref(props.filters.date_from || '')
const filterDateTo = ref(props.filters.date_to || '')

function applyFilters() {
    const params: Record<string, string> = {}
    if (filterAction.value) params.action = filterAction.value
    if (filterDateFrom.value) params.date_from = filterDateFrom.value
    if (filterDateTo.value) params.date_to = filterDateTo.value
    router.get('/admin/audit-logs', params, { preserveState: true })
}

function clearFilters() {
    filterAction.value = ''
    filterDateFrom.value = ''
    filterDateTo.value = ''
    router.get('/admin/audit-logs')
}

function formatDateTime(date: string) {
    return new Date(date).toLocaleString('en-GB', {
        day: 'numeric', month: 'short', year: 'numeric',
        hour: '2-digit', minute: '2-digit', second: '2-digit'
    })
}

function actionBadge(action: string) {
    if (action.includes('login_failure') || action.includes('cancel')) return 'bg-red-50 text-red-700'
    if (action.includes('login_success') || action.includes('created')) return 'bg-green-50 text-green-700'
    if (action.includes('viewed') || action.includes('downloaded')) return 'bg-blue-50 text-blue-700'
    if (action.includes('updated') || action.includes('changed')) return 'bg-amber-50 text-amber-700'
    return 'bg-gray-100 text-gray-600'
}

function actionLabel(action: string) {
    return action.replace(/_/g, ' ').replace(/\b\w/g, c => c.toUpperCase())
}

function shortTarget(target: string | null) {
    if (!target) return ''
    return target.split('\\').pop() || target
}
</script>

<template>
    <div>
        <div class="mb-6">
            <h1 class="text-2xl font-bold text-gray-900">Audit Logs</h1>
            <p class="text-sm text-gray-500">Track all system activities and security events.</p>
        </div>

        <!-- Filters -->
        <div class="bg-white rounded-xl border border-gray-200 p-4 mb-4">
            <div class="grid grid-cols-1 sm:grid-cols-4 gap-3 items-end">
                <div>
                    <label class="block text-xs font-medium text-gray-500 mb-1">Action</label>
                    <select v-model="filterAction" class="w-full rounded-lg border-gray-300 text-sm">
                        <option value="">All</option>
                        <option v-for="a in actions" :key="a" :value="a">{{ actionLabel(a) }}</option>
                    </select>
                </div>
                <div>
                    <label class="block text-xs font-medium text-gray-500 mb-1">From</label>
                    <input type="date" v-model="filterDateFrom" class="w-full rounded-lg border-gray-300 text-sm" />
                </div>
                <div>
                    <label class="block text-xs font-medium text-gray-500 mb-1">To</label>
                    <input type="date" v-model="filterDateTo" class="w-full rounded-lg border-gray-300 text-sm" />
                </div>
                <div class="flex gap-2">
                    <button @click="applyFilters" class="px-4 py-2 bg-gray-900 text-white text-sm font-medium rounded-lg hover:bg-gray-800 transition-colors">Filter</button>
                    <button @click="clearFilters" class="px-4 py-2 bg-gray-100 text-gray-600 text-sm font-medium rounded-lg hover:bg-gray-200 transition-colors">Clear</button>
                </div>
            </div>
        </div>

        <!-- Table -->
        <div class="bg-white rounded-xl border border-gray-200 overflow-hidden">
            <table class="min-w-full text-sm">
                <thead class="bg-gray-50 text-left">
                    <tr>
                        <th class="px-4 py-3 font-medium text-gray-500">Time</th>
                        <th class="px-4 py-3 font-medium text-gray-500">User</th>
                        <th class="px-4 py-3 font-medium text-gray-500">Role</th>
                        <th class="px-4 py-3 font-medium text-gray-500">Action</th>
                        <th class="px-4 py-3 font-medium text-gray-500">Target</th>
                        <th class="px-4 py-3 font-medium text-gray-500">IP</th>
                        <th class="px-4 py-3 font-medium text-gray-500">Status</th>
                    </tr>
                </thead>
                <tbody class="divide-y divide-gray-100">
                    <tr v-for="log in logs.data" :key="log.id">
                        <td class="px-4 py-3 text-gray-600 text-xs whitespace-nowrap">{{ formatDateTime(log.created_at) }}</td>
                        <td class="px-4 py-3 text-gray-900 text-xs">{{ log.user?.name || '—' }}</td>
                        <td class="px-4 py-3 text-gray-500 text-xs capitalize">{{ log.role || '—' }}</td>
                        <td class="px-4 py-3">
                            <span :class="actionBadge(log.action)" class="text-xs font-medium px-2 py-0.5 rounded-full">
                                {{ actionLabel(log.action) }}
                            </span>
                        </td>
                        <td class="px-4 py-3 text-gray-500 text-xs">
                            <span v-if="log.target_type">{{ shortTarget(log.target_type) }} #{{ log.target_id }}</span>
                            <span v-else>—</span>
                        </td>
                        <td class="px-4 py-3 text-gray-400 text-xs font-mono">{{ log.ip_address || '—' }}</td>
                        <td class="px-4 py-3">
                            <span :class="log.success ? 'text-green-600' : 'text-red-600'" class="text-xs font-medium">
                                {{ log.success ? 'OK' : 'FAIL' }}
                            </span>
                        </td>
                    </tr>
                    <tr v-if="logs.data.length === 0">
                        <td colspan="7" class="px-4 py-8 text-center text-gray-400">No audit logs found.</td>
                    </tr>
                </tbody>
            </table>
        </div>

        <!-- Pagination -->
        <div v-if="logs.links.length > 3" class="mt-4 flex justify-center gap-1">
            <template v-for="link in logs.links" :key="link.label">
                <a
                    v-if="link.url"
                    :href="link.url"
                    class="px-3 py-1 text-sm rounded-md border"
                    :class="link.active ? 'bg-gray-900 text-white border-gray-900' : 'bg-white text-gray-600 border-gray-300 hover:bg-gray-50'"
                    v-html="link.label"
                />
                <span v-else class="px-3 py-1 text-sm text-gray-300" v-html="link.label" />
            </template>
        </div>
    </div>
</template>
