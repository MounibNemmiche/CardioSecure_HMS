<script setup lang="ts">
import DoctorLayout from '@/layouts/DoctorLayout.vue'

defineOptions({ layout: DoctorLayout })

const props = defineProps<{
    patients: {
        id: number
        name: string
        email: string
        blood_type: string | null
        allergies: string | null
        appointments_count: number
        last_visit: string | null
    }[]
}>()

function formatDate(date: string | null) {
    if (!date) return '—'
    return new Date(date).toLocaleDateString('en-GB', { day: 'numeric', month: 'short', year: 'numeric' })
}
</script>

<template>
    <div>
        <div class="mb-6">
            <h1 class="text-2xl font-bold text-gray-900">My Patients</h1>
            <p class="text-sm text-gray-500">Patients you have appointments with.</p>
        </div>

        <div class="bg-white rounded-xl border border-gray-200 overflow-hidden">
            <table class="min-w-full text-sm">
                <thead class="bg-gray-50 text-left">
                    <tr>
                        <th class="px-4 py-3 font-medium text-gray-500">Name</th>
                        <th class="px-4 py-3 font-medium text-gray-500">Email</th>
                        <th class="px-4 py-3 font-medium text-gray-500">Blood Type</th>
                        <th class="px-4 py-3 font-medium text-gray-500">Allergies</th>
                        <th class="px-4 py-3 font-medium text-gray-500">Appointments</th>
                        <th class="px-4 py-3 font-medium text-gray-500">Last Visit</th>
                        <th class="px-4 py-3 font-medium text-gray-500 text-right">Actions</th>
                    </tr>
                </thead>
                <tbody class="divide-y divide-gray-100">
                    <tr v-for="patient in patients" :key="patient.id">
                        <td class="px-4 py-3 text-gray-900 font-medium">{{ patient.name }}</td>
                        <td class="px-4 py-3 text-gray-600">{{ patient.email }}</td>
                        <td class="px-4 py-3 text-gray-600">{{ patient.blood_type || '—' }}</td>
                        <td class="px-4 py-3 text-gray-500 max-w-48 truncate">{{ patient.allergies || '—' }}</td>
                        <td class="px-4 py-3 text-gray-600">{{ patient.appointments_count }}</td>
                        <td class="px-4 py-3 text-gray-600">{{ formatDate(patient.last_visit) }}</td>
                        <td class="px-4 py-3 text-right">
                            <a :href="`/doctor/patients/${patient.id}/records`" class="text-emerald-700 hover:text-emerald-900 text-xs font-medium">
                                View Records
                            </a>
                        </td>
                    </tr>
                    <tr v-if="patients.length === 0">
                        <td colspan="7" class="px-4 py-8 text-center text-gray-400">No patients found.</td>
                    </tr>
                </tbody>
            </table>
        </div>
    </div>
</template>
