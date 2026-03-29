<script setup lang="ts">
import { router } from '@inertiajs/vue3'
import AdminLayout from '@/layouts/AdminLayout.vue'
import type { InsurancePlan } from '@/types'

defineOptions({ layout: AdminLayout })

defineProps<{ plans: InsurancePlan[] }>()

function deletePlan(id: number) {
    if (confirm('Are you sure you want to delete this insurance plan?')) {
        router.delete(`/admin/insurance-plans/${id}`)
    }
}
</script>

<template>
    <div>
        <div class="flex items-center justify-between mb-6">
            <div>
                <h1 class="text-2xl font-bold text-gray-900">Insurance Plans</h1>
                <p class="text-sm text-gray-500">Manage the master list of insurance plans.</p>
            </div>
            <a href="/admin/insurance-plans/create" class="px-4 py-2 bg-red-600 text-white text-sm font-medium rounded-lg hover:bg-red-700 transition-colors">
                Add Plan
            </a>
        </div>

        <div class="bg-white rounded-xl border border-gray-200 overflow-hidden">
            <table class="min-w-full text-sm">
                <thead class="bg-gray-50 text-left">
                    <tr>
                        <th class="px-4 py-3 font-medium text-gray-500">Name</th>
                        <th class="px-4 py-3 font-medium text-gray-500">Code</th>
                        <th class="px-4 py-3 font-medium text-gray-500">Status</th>
                        <th class="px-4 py-3 font-medium text-gray-500 text-right">Actions</th>
                    </tr>
                </thead>
                <tbody class="divide-y divide-gray-100">
                    <tr v-for="plan in plans" :key="plan.id">
                        <td class="px-4 py-3 text-gray-900 font-medium">{{ plan.name }}</td>
                        <td class="px-4 py-3 text-gray-500 font-mono text-xs">{{ plan.code }}</td>
                        <td class="px-4 py-3">
                            <span :class="plan.is_active ? 'bg-green-100 text-green-700' : 'bg-gray-100 text-gray-500'" class="text-xs font-medium px-2 py-1 rounded-full">
                                {{ plan.is_active ? 'Active' : 'Inactive' }}
                            </span>
                        </td>
                        <td class="px-4 py-3 text-right space-x-2">
                            <a :href="`/admin/insurance-plans/${plan.id}/edit`" class="text-blue-600 hover:text-blue-800 text-xs font-medium">Edit</a>
                            <button @click="deletePlan(plan.id)" class="text-red-600 hover:text-red-800 text-xs font-medium">Delete</button>
                        </td>
                    </tr>
                    <tr v-if="plans.length === 0">
                        <td colspan="4" class="px-4 py-8 text-center text-gray-400">No insurance plans yet.</td>
                    </tr>
                </tbody>
            </table>
        </div>
    </div>
</template>
