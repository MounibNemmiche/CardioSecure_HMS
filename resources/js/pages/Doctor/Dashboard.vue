<script setup lang="ts">
import StatCard from '@/components/StatCard.vue'
import DoctorLayout from '@/layouts/DoctorLayout.vue'
import { usePage } from '@inertiajs/vue3'

defineOptions({ layout: DoctorLayout })

const page = usePage()
const userName = (page.props.auth as { user: { name: string } }).user.name.split(' ')[0]

const props = defineProps<{
    appointmentsToday: number
    totalPatients: number
    totalRecords: number
    upcomingAppointments: {
        id: number
        date: string
        time: string
        patient: string
        type: string
    }[]
}>()

function formatDate(date: string) {
    return new Date(date).toLocaleDateString('en-GB', { day: 'numeric', month: 'short' })
}

function formatTime(time: string) {
    return time.substring(0, 5)
}
</script>

<template>
    <div>
        <h1 class="text-2xl font-bold text-gray-900 mb-1">Welcome back, {{ userName }}</h1>
        <p class="text-sm text-gray-500 mb-8">Cardiology department overview</p>

        <div class="grid grid-cols-1 sm:grid-cols-3 gap-4 mb-8">
            <StatCard label="Appointments Today" :value="appointmentsToday" color="blue">
                <template #icon>
                    <svg class="w-5 h-5" fill="none" viewBox="0 0 24 24" stroke="currentColor">
                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M8 7V3m8 4V3m-9 8h10M5 21h14a2 2 0 002-2V7a2 2 0 00-2-2H5a2 2 0 00-2 2v12a2 2 0 002 2z" />
                    </svg>
                </template>
            </StatCard>
            <StatCard label="Patients" :value="totalPatients" color="emerald">
                <template #icon>
                    <svg class="w-5 h-5" fill="none" viewBox="0 0 24 24" stroke="currentColor">
                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M17 20h5v-2a3 3 0 00-5.356-1.857M17 20H7m10 0v-2c0-.656-.126-1.283-.356-1.857M7 20H2v-2a3 3 0 015.356-1.857M7 20v-2c0-.656.126-1.283.356-1.857m0 0a5.002 5.002 0 019.288 0M15 7a3 3 0 11-6 0 3 3 0 016 0z" />
                    </svg>
                </template>
            </StatCard>
            <StatCard label="Records Created" :value="totalRecords" color="violet">
                <template #icon>
                    <svg class="w-5 h-5" fill="none" viewBox="0 0 24 24" stroke="currentColor">
                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M9 12h6m-6 4h6m2 5H7a2 2 0 01-2-2V5a2 2 0 012-2h5.586a1 1 0 01.707.293l5.414 5.414a1 1 0 01.293.707V19a2 2 0 01-2 2z" />
                    </svg>
                </template>
            </StatCard>
        </div>

        <div v-if="upcomingAppointments.length > 0">
            <h2 class="text-lg font-semibold text-gray-900 mb-3">Upcoming Appointments</h2>
            <div class="bg-white rounded-xl border border-gray-200 overflow-hidden">
                <table class="min-w-full text-sm">
                    <thead class="bg-gray-50 text-left">
                        <tr>
                            <th class="px-4 py-3 font-medium text-gray-500">Date</th>
                            <th class="px-4 py-3 font-medium text-gray-500">Time</th>
                            <th class="px-4 py-3 font-medium text-gray-500">Patient</th>
                            <th class="px-4 py-3 font-medium text-gray-500">Type</th>
                        </tr>
                    </thead>
                    <tbody class="divide-y divide-gray-100">
                        <tr v-for="appt in upcomingAppointments" :key="appt.id" class="hover:bg-gray-50 transition-colors duration-150">
                            <td class="px-4 py-3 text-gray-900">{{ formatDate(appt.date) }}</td>
                            <td class="px-4 py-3 text-gray-600">{{ formatTime(appt.time) }}</td>
                            <td class="px-4 py-3 text-gray-900">{{ appt.patient }}</td>
                            <td class="px-4 py-3 text-gray-600 capitalize">{{ appt.type.replace('_', ' ') }}</td>
                        </tr>
                    </tbody>
                </table>
            </div>
        </div>
        <div v-else class="bg-white rounded-xl border border-gray-200 p-8 text-center">
            <p class="text-sm text-gray-400">No upcoming appointments scheduled.</p>
        </div>
    </div>
</template>
