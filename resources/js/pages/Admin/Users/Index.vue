<script setup lang="ts">
import AdminLayout from '@/layouts/AdminLayout.vue'

defineOptions({ layout: AdminLayout })

defineProps<{
    users: {
        id: number
        name: string
        email: string
        role: string
        verified: boolean
        mfa: boolean
        created_at: string
    }[]
}>()
</script>

<template>
    <div>
        <div class="flex items-center justify-between mb-6">
            <div>
                <h1 class="text-2xl font-bold text-gray-900">User Management</h1>
                <p class="text-sm text-gray-500">Create and manage system users.</p>
            </div>
            <a href="/admin/users/create" class="px-4 py-2 bg-red-600 text-white text-sm font-medium rounded-lg hover:bg-red-700 transition-colors">
                Add User
            </a>
        </div>

        <div class="bg-white rounded-xl border border-gray-200 overflow-hidden">
            <table class="min-w-full text-sm">
                <thead class="bg-gray-50 text-left">
                    <tr>
                        <th class="px-4 py-3 font-medium text-gray-500">Name</th>
                        <th class="px-4 py-3 font-medium text-gray-500">Email</th>
                        <th class="px-4 py-3 font-medium text-gray-500">Role</th>
                        <th class="px-4 py-3 font-medium text-gray-500">Verified</th>
                        <th class="px-4 py-3 font-medium text-gray-500">MFA</th>
                        <th class="px-4 py-3 font-medium text-gray-500 text-right">Actions</th>
                    </tr>
                </thead>
                <tbody class="divide-y divide-gray-100">
                    <tr v-for="user in users" :key="user.id">
                        <td class="px-4 py-3 text-gray-900 font-medium">{{ user.name }}</td>
                        <td class="px-4 py-3 text-gray-600">{{ user.email }}</td>
                        <td class="px-4 py-3">
                            <span class="text-xs font-medium px-2 py-1 rounded-full"
                                :class="{
                                    'bg-red-100 text-red-700': user.role === 'admin',
                                    'bg-emerald-100 text-emerald-700': user.role === 'doctor',
                                    'bg-violet-100 text-violet-700': user.role === 'staff',
                                    'bg-blue-100 text-blue-700': user.role === 'patient',
                                }"
                            >{{ user.role }}</span>
                        </td>
                        <td class="px-4 py-3">
                            <span :class="user.verified ? 'text-green-600' : 'text-gray-400'">{{ user.verified ? 'Yes' : 'No' }}</span>
                        </td>
                        <td class="px-4 py-3">
                            <span :class="user.mfa ? 'text-green-600' : 'text-gray-400'">{{ user.mfa ? 'Yes' : 'No' }}</span>
                        </td>
                        <td class="px-4 py-3 text-right">
                            <a :href="`/admin/users/${user.id}/edit`" class="text-blue-600 hover:text-blue-800 text-xs font-medium">Edit</a>
                        </td>
                    </tr>
                </tbody>
            </table>
        </div>
    </div>
</template>
