<script setup lang="ts">
import { ref } from 'vue'
import { router } from '@inertiajs/vue3'
import PatientLayout from '@/layouts/PatientLayout.vue'
import CalendarView from '@/components/CalendarView.vue'
import type { Appointment } from '@/types'

defineOptions({ layout: PatientLayout })

const props = defineProps<{
    appointments: {
        data: Appointment[]
        links: { url: string | null; label: string; active: boolean }[]
    }
    allAppointments: Appointment[]
    visitTypes: Record<string, string>
}>()

const view = ref<'list' | 'calendar'>('list')

function formatDate(date: string) {
    return new Date(date).toLocaleDateString('en-GB', { day: 'numeric', month: 'short', year: 'numeric' })
}

function formatTime(time: string) {
    return time.substring(0, 5)
}

function statusBadge(status: string) {
    const map: Record<string, string> = {
        scheduled: 'bg-blue-100 text-blue-700',
        completed: 'bg-green-100 text-green-700',
        cancelled: 'bg-red-100 text-red-600',
        no_show: 'bg-yellow-100 text-yellow-700',
    }
    return map[status] || 'bg-gray-100 text-gray-500'
}

function statusLabel(status: string) {
    const map: Record<string, string> = {
        scheduled: 'Scheduled',
        completed: 'Completed',
        cancelled: 'Cancelled',
        no_show: 'No Show',
    }
    return map[status] || status
}

function cancelAppointment(id: number) {
    if (confirm('Are you sure you want to cancel this appointment?')) {
        router.patch(`/patient/appointments/${id}/cancel`)
    }
}

function googleCalendarUrl(appt: Appointment) {
    const dateStr = appt.appointment_date.substring(0, 10).replace(/-/g, '')
    const timeStr = appt.appointment_time.substring(0, 5).replace(':', '')
    const start = `${dateStr}T${timeStr}00`
    const [h, m] = appt.appointment_time.substring(0, 5).split(':').map(Number)
    const endMin = h * 60 + m + 30
    const endH = String(Math.floor(endMin / 60)).padStart(2, '0')
    const endM = String(endMin % 60).padStart(2, '0')
    const end = `${dateStr}T${endH}${endM}00`
    const title = encodeURIComponent(`CardioSecure: ${props.visitTypes[appt.visit_type] || appt.visit_type}`)
    const details = encodeURIComponent(`Doctor: ${appt.doctor?.user?.name || 'N/A'}`)
    return `https://calendar.google.com/calendar/render?action=TEMPLATE&text=${title}&dates=${start}/${end}&details=${details}`
}
</script>

<template>
    <div>
        <div class="flex items-center justify-between mb-6">
            <div>
                <h1 class="text-2xl font-bold text-gray-900">My Appointments</h1>
                <p class="text-sm text-gray-500">View and manage your appointments.</p>
            </div>
            <div class="flex items-center gap-3">
                <!-- View toggle -->
                <div class="flex bg-gray-100 rounded-lg p-0.5">
                    <button
                        @click="view = 'list'"
                        :class="view === 'list' ? 'bg-white shadow-sm text-gray-900' : 'text-gray-500 hover:text-gray-700'"
                        class="px-3 py-1.5 text-xs font-medium rounded-md transition-all"
                    >
                        <svg class="w-4 h-4 inline-block mr-1" fill="none" viewBox="0 0 24 24" stroke="currentColor"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M4 6h16M4 10h16M4 14h16M4 18h16" /></svg>
                        List
                    </button>
                    <button
                        @click="view = 'calendar'"
                        :class="view === 'calendar' ? 'bg-white shadow-sm text-gray-900' : 'text-gray-500 hover:text-gray-700'"
                        class="px-3 py-1.5 text-xs font-medium rounded-md transition-all"
                    >
                        <svg class="w-4 h-4 inline-block mr-1" fill="none" viewBox="0 0 24 24" stroke="currentColor"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M8 7V3m8 4V3m-9 8h10M5 21h14a2 2 0 002-2V7a2 2 0 00-2-2H5a2 2 0 00-2 2v12a2 2 0 002 2z" /></svg>
                        Calendar
                    </button>
                </div>
                <a href="/patient/appointments/create" class="px-4 py-2 bg-blue-800 text-white text-sm font-medium rounded-lg hover:bg-blue-900 transition-colors">
                    Book Appointment
                </a>
            </div>
        </div>

        <!-- Calendar View -->
        <CalendarView
            v-if="view === 'calendar'"
            :appointments="allAppointments"
            :visit-types="visitTypes"
            :show-doctor="true"
            accent-color="blue"
        />

        <!-- List View -->
        <template v-else>
            <div class="bg-white rounded-xl border border-gray-200 overflow-hidden">
                <table class="min-w-full text-sm">
                    <thead class="bg-gray-50 text-left">
                        <tr>
                            <th class="px-4 py-3 font-medium text-gray-500">Date</th>
                            <th class="px-4 py-3 font-medium text-gray-500">Time</th>
                            <th class="px-4 py-3 font-medium text-gray-500">Doctor</th>
                            <th class="px-4 py-3 font-medium text-gray-500">Type</th>
                            <th class="px-4 py-3 font-medium text-gray-500">Status</th>
                            <th class="px-4 py-3 font-medium text-gray-500 text-right">Actions</th>
                        </tr>
                    </thead>
                    <tbody class="divide-y divide-gray-100">
                        <tr v-for="appt in appointments.data" :key="appt.id">
                            <td class="px-4 py-3 text-gray-900 font-medium">{{ formatDate(appt.appointment_date) }}</td>
                            <td class="px-4 py-3 text-gray-600">{{ formatTime(appt.appointment_time) }}</td>
                            <td class="px-4 py-3 text-gray-600">{{ appt.doctor?.user?.name }}</td>
                            <td class="px-4 py-3 text-gray-600">{{ visitTypes[appt.visit_type] || appt.visit_type }}</td>
                            <td class="px-4 py-3">
                                <span :class="statusBadge(appt.status)" class="text-xs font-medium px-2 py-1 rounded-full">
                                    {{ statusLabel(appt.status) }}
                                </span>
                            </td>
                            <td class="px-4 py-3 text-right space-x-2">
                                <a v-if="appt.status === 'scheduled'" :href="googleCalendarUrl(appt)" target="_blank" class="text-blue-600 hover:text-blue-800 text-xs font-medium">
                                    Calendar
                                </a>
                                <button v-if="appt.status === 'scheduled'" @click="cancelAppointment(appt.id)" class="text-red-600 hover:text-red-800 text-xs font-medium">
                                    Cancel
                                </button>
                            </td>
                        </tr>
                        <tr v-if="appointments.data.length === 0">
                            <td colspan="6" class="px-4 py-8 text-center text-gray-400">No appointments found.</td>
                        </tr>
                    </tbody>
                </table>
            </div>

            <!-- Pagination -->
            <div v-if="appointments.links.length > 3" class="mt-4 flex justify-center gap-1">
                <template v-for="link in appointments.links" :key="link.label">
                    <a
                        v-if="link.url"
                        :href="link.url"
                        class="px-3 py-1 text-sm rounded-md border"
                        :class="link.active ? 'bg-blue-800 text-white border-blue-800' : 'bg-white text-gray-600 border-gray-300 hover:bg-gray-50'"
                        v-html="link.label"
                    />
                    <span v-else class="px-3 py-1 text-sm text-gray-300" v-html="link.label" />
                </template>
            </div>
        </template>
    </div>
</template>
