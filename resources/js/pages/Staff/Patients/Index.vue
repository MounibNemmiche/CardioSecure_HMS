<script setup lang="ts">
import StaffLayout from '@/layouts/StaffLayout.vue'

defineOptions({ layout: StaffLayout })

defineProps<{
    patients: {
        id: number
        name: string
        email: string
        blood_type: string | null
        allergies: string | null
        insurance_plan: string | null
        appointments_count: number
    }[]
}>()
</script>

<template>
    <div>
        <h1 class="text-2xl font-bold text-gray-900 mb-1">Patients</h1>
        <p class="text-sm text-gray-500 mb-6">View patient profiles and insurance information.</p>

        <div class="bg-white border border-gray-200 rounded-xl overflow-hidden">
            <table class="w-full text-sm">
                <thead class="bg-gray-50 border-b border-gray-200">
                    <tr>
                        <th class="text-left px-4 py-3 font-medium text-gray-600">Name</th>
                        <th class="text-left px-4 py-3 font-medium text-gray-600">Email</th>
                        <th class="text-left px-4 py-3 font-medium text-gray-600">Blood Type</th>
                        <th class="text-left px-4 py-3 font-medium text-gray-600">Insurance</th>
                        <th class="text-left px-4 py-3 font-medium text-gray-600">Appointments</th>
                        <th class="text-left px-4 py-3 font-medium text-gray-600">Actions</th>
                    </tr>
                </thead>
                <tbody class="divide-y divide-gray-100">
                    <tr v-for="p in patients" :key="p.id" class="hover:bg-gray-50">
                        <td class="px-4 py-3 font-medium text-gray-900">{{ p.name }}</td>
                        <td class="px-4 py-3 text-gray-600">{{ p.email }}</td>
                        <td class="px-4 py-3 text-gray-600">{{ p.blood_type || '—' }}</td>
                        <td class="px-4 py-3 text-gray-600">{{ p.insurance_plan || '—' }}</td>
                        <td class="px-4 py-3 text-gray-600">{{ p.appointments_count }}</td>
                        <td class="px-4 py-3">
                            <a :href="`/staff/patients/${p.id}`" class="text-violet-700 hover:text-violet-900 font-medium">
                                View Details
                            </a>
                        </td>
                    </tr>
                </tbody>
            </table>
        </div>
    </div>
</template>
